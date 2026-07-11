<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\ContactMessage;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBusinessController extends Controller
{
    public function analytics()
    {
        $orders = Order::whereNotIn('status', ['cancelled', 'returned']);
        $revenue = (clone $orders)->sum('total_amount');
        $discounts = (clone $orders)->sum('discount_amount');
        $expenses = Expense::sum('amount');
        $cost = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_items.quantity * COALESCE(products.cost_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $dailySales = Order::selectRaw('DATE(created_at) as day, SUM(total_amount) as total, COUNT(*) as orders')
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        return view('admin.business.analytics', [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'grossProfit' => $revenue - $cost - $expenses,
            'discounts' => $discounts,
            'dailySales' => $dailySales,
            'topProducts' => $topProducts,
        ]);
    }

    public function inventory()
    {
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->paginate(20);

        $deadStockProducts = Product::with('category')
            ->whereDoesntHave('orderItems')
            ->oldest()
            ->limit(20)
            ->get();

        return view('admin.business.inventory', compact('lowStockProducts', 'deadStockProducts'));
    }

    public function customers()
    {
        $customers = User::withCount('orders')
            ->withSum('orders', 'total_amount')
            ->where('role', 'customer')
            ->orderByDesc('orders_sum_total_amount')
            ->paginate(20);

        return view('admin.business.customers', compact('customers'));
    }

    public function expenses()
    {
        $expenses = Expense::latest('spent_on')->paginate(20);
        $categoryTotals = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.business.expenses', compact('expenses', 'categoryTotals'));
    }

    public function storeExpense(Request $request)
    {
        Expense::create($request->validate([
            'category' => 'required|string|max:80',
            'title' => 'required|string|max:160',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'payment_method' => 'nullable|string|max:80',
            'notes' => 'nullable|string|max:1000',
        ]));

        return back()->with('success', 'Expense saved.');
    }

    public function notifications()
    {
        $notifications = AdminNotification::latest()->paginate(25);
        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $contactMessages = ContactMessage::whereNull('read_at')->count();

        return view('admin.business.notifications', compact('notifications', 'lowStockCount', 'pendingOrders', 'contactMessages'));
    }

    public function reports()
    {
        return view('admin.business.reports');
    }

    public function export(string $type): StreamedResponse
    {
        $allowed = ['sales', 'customers', 'products', 'inventory', 'expenses'];
        abort_unless(in_array($type, $allowed, true), 404);

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            match ($type) {
                'sales' => $this->writeSalesReport($handle),
                'customers' => $this->writeCustomerReport($handle),
                'products' => $this->writeProductReport($handle),
                'inventory' => $this->writeInventoryReport($handle),
                'expenses' => $this->writeExpenseReport($handle),
            };

            fclose($handle);
        }, "{$type}-report.csv", ['Content-Type' => 'text/csv']);
    }

    public function invoice(Order $order)
    {
        $order->load('items.product');

        return view('admin.orders.invoice', compact('order'));
    }

    private function writeSalesReport($handle): void
    {
        fputcsv($handle, ['Order', 'Customer', 'Status', 'Payment', 'Total', 'Date']);
        Order::latest()->chunk(100, function ($orders) use ($handle) {
            foreach ($orders as $order) {
                fputcsv($handle, [$order->order_number, $order->customer_name, $order->status, $order->payment_status, $order->total_amount, $order->created_at]);
            }
        });
    }

    private function writeCustomerReport($handle): void
    {
        fputcsv($handle, ['Name', 'Email', 'Orders', 'Total Spent']);
        User::withCount('orders')->withSum('orders', 'total_amount')->chunk(100, function ($users) use ($handle) {
            foreach ($users as $user) {
                fputcsv($handle, [$user->name, $user->email, $user->orders_count, $user->orders_sum_total_amount ?? 0]);
            }
        });
    }

    private function writeProductReport($handle): void
    {
        fputcsv($handle, ['Product', 'SKU', 'Price', 'Stock', 'Status']);
        Product::chunk(100, function ($products) use ($handle) {
            foreach ($products as $product) {
                fputcsv($handle, [$product->name, $product->sku, $product->current_price, $product->stock, $product->visibility_status ?? 'active']);
            }
        });
    }

    private function writeInventoryReport($handle): void
    {
        fputcsv($handle, ['Product', 'Stock', 'Threshold', 'Valuation']);
        Product::chunk(100, function ($products) use ($handle) {
            foreach ($products as $product) {
                fputcsv($handle, [$product->name, $product->stock, $product->low_stock_threshold ?? 5, $product->stock * ($product->cost_price ?? 0)]);
            }
        });
    }

    private function writeExpenseReport($handle): void
    {
        fputcsv($handle, ['Date', 'Category', 'Title', 'Amount']);
        Expense::latest('spent_on')->chunk(100, function ($expenses) use ($handle) {
            foreach ($expenses as $expense) {
                fputcsv($handle, [$expense->spent_on?->toDateString(), $expense->category, $expense->title, $expense->amount]);
            }
        });
    }
}

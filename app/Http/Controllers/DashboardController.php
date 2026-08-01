<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Customer statistics
        $ordersCount = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total_amount');
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $addressesCount = Address::where('user_id', $user->id)->count();

        // Recent orders for user
        $recentOrders = Order::where('user_id', $user->id)
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get();

        // Wishlist items preview
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product.category')
            ->latest()
            ->take(4)
            ->get();

        // Platform overview stats (for admins)
        $adminStats = null;
        if ($user->isAdmin()) {
            $adminStats = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
                'total_products' => Product::count(),
                'low_stock_products' => Product::where('stock', '<=', 5)->count(),
                'total_customers' => User::where('role', 'customer')->count(),
            ];
        }

        return view('dashboard', compact(
            'user',
            'ordersCount',
            'totalSpent',
            'wishlistCount',
            'addressesCount',
            'recentOrders',
            'wishlistItems',
            'adminStats'
        ));
    }
}

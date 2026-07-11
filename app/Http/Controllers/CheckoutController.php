<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Mail\OrderConfirmation;
use App\Services\CartService;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $otpService;

    public function __construct(CartService $cartService, OTPService $otpService)
    {
        $this->cartService = $cartService;
        $this->otpService = $otpService;
    }

    /**
     * Show the checkout form
     */
    public function index()
    {
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate cart before checkout
        $validation = $this->cartService->validateCart();
        if (!$validation['valid']) {
            return redirect()->route('cart.index')->with('error', $validation['message']);
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Process the checkout
     */
    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate cart
        $validation = $this->cartService->validateCart();
        if (!$validation['valid']) {
            return redirect()->route('cart.index')->with('error', $validation['message']);
        }

        // Validate checkout form
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string|min:10|max:1000',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|in:cod,sslcommerz,bkash,nagad',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $coupon = Session::get('coupon');

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?: $request->shipping_address,
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'discount_amount' => $cart->discount_amount,
                'total_amount' => $cart->grand_total,
                'currency' => 'BDT',
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Create order items and decrement stock with row lock to prevent race conditions
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'price' => $cartItem->price,
                    'quantity' => $cartItem->quantity,
                    'total' => $cartItem->subtotal,
                ]);

                // Use pessimistic locking to prevent overselling
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();
                if ($product && $product->stock >= $cartItem->quantity) {
                    $product->decrement('stock', $cartItem->quantity);
                } else {
                    throw new \Exception("Insufficient stock for {$cartItem->product->name}");
                }
            }

            if ($coupon) {
                \App\Models\Coupon::where('id', $coupon['id'])->increment('used_count');
                Session::forget('coupon');
            }

            // Create fulfillment requests for seller-split orders
            $order->load('items.product');
            $dropshippingService = app(\App\Services\DropshippingService::class);
            $dropshippingService->createFulfillmentRequests($order);

            DB::commit();

            // Clear the cart
            $this->cartService->clearCart();

            // For COD, send OTP verification
            if ($request->payment_method === 'cod') {
                $this->otpService->generateAndSend($order);
                return redirect()->route('orders.otp', $order)->with('success', 'Order placed! Please verify with OTP sent to your phone.');
            }

            // Queue order confirmation email
            if ($order->customer_email) {
                Mail::to($order->customer_email)->queue(new OrderConfirmation($order));
            }

            return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Session::forget('coupon');
            return back()->with('error', 'Failed to process order. Please try again.')->withInput();
        }
    }

    /**
     * Show order success page
     */
    public function success(Order $order)
    {
        // Only show if the order belongs to the authenticated user or if it's a guest order
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('checkout.success', compact('order'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function adminIndex(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->payment_status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function adminShow(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,packed,shipped,delivered,returned,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:120',
            'courier_name' => 'nullable|string|max:120',
        ]);

        $updates = $validated;

        if ($validated['status'] === 'shipped' && !$order->shipped_at) {
            $updates['shipped_at'] = now();
        }

        if ($validated['status'] === 'packed' && !$order->packed_at) {
            $updates['packed_at'] = now();
        }

        if ($validated['status'] === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($validated['status'] === 'returned' && !$order->returned_at) {
            $updates['returned_at'] = now();
        }

        $order->update($updates);

        return back()->with('success', 'Order status updated.');
    }

    public function adminDashboard()
    {
        $stats = [
            'orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 5)->count(),
            'customers' => User::where('role', 'customer')->count(),
        ];

        $recentOrders = Order::with('user')->latest()->limit(8)->get();
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }

    /**
     * Show OTP verification form for COD orders.
     */
    public function showOtpForm(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method !== 'cod') {
            return redirect()->route('checkout.success', $order);
        }

        return view('checkout.otp', compact('order'));
    }

    /**
     * Verify OTP for COD order.
     */
    public function verifyOtp(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($this->otpService->verify($order, $validated['otp'])) {
            $order->update(['status' => 'confirmed']);
            return redirect()->route('checkout.success', $order)->with('success', 'Order confirmed!');
        }

        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    /**
     * Resend OTP for COD order.
     */
    public function resendOtp(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $this->otpService->generateAndSend($order);

        return back()->with('success', 'A new OTP has been sent to your phone.');
    }
}

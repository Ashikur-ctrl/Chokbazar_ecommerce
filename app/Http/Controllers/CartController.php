<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the cart
     */
    public function index(): View
    {
        $cartSummary = $this->cartService->getCartSummary();

        return view('cart.index', compact('cartSummary'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $result = $this->cartService->addToCart($productId, $quantity);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $maxStock = Product::where('id', $request->product_id)->value('stock') ?: 999;
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => "required|integer|min:1|max:{$maxStock}",
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $result = $this->cartService->updateQuantity($productId, $quantity);

        if ($request->expectsJson()) {
            return response()->json($result + ['cart' => $this->cartService->getCartSummary()]);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');

        $result = $this->cartService->removeFromCart($productId);

        if ($request->expectsJson()) {
            return response()->json($result + ['cart' => $this->cartService->getCartSummary()]);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $result = $this->cartService->clearCart();

        if (request()->expectsJson()) {
            return response()->json($result + ['cart' => $this->cartService->getCartSummary()]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Get cart summary (AJAX)
     */
    public function summary()
    {
        return response()->json($this->cartService->getCartSummary());
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart()
    {
        $validation = $this->cartService->validateCart();

        return response()->json($validation);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $cart = $this->cartService->getCart();
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon || ! $coupon->isValidFor($cart->subtotal)) {
            return back()->with('error', 'Coupon code is invalid or not available for this cart.');
        }

        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $coupon->discountFor($cart->subtotal),
        ]);

        return back()->with('success', 'Coupon applied successfully.');
    }

    public function removeCoupon()
    {
        Session::forget('coupon');

        return back()->with('success', 'Coupon removed.');
    }
}

<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create cart for current session/user
     */
    public function getCart(): Cart
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        // Try to find existing cart
        $cart = Cart::where('session_id', $sessionId)
            ->when($userId, function ($query) use ($userId) {
                return $query->orWhere('user_id', $userId);
            })
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
            ]);
        } elseif ($userId && !$cart->user_id) {
            // Associate cart with user if they just logged in
            $cart->update(['user_id' => $userId]);
        }

        return $cart->load('items.product');
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity = 1, array $options = []): array
    {
        $product = Product::findOrFail($productId);

        // Check if product is available
        if (!$product->is_active) {
            return ['success' => false, 'message' => 'Product is not available.'];
        }

        if (!$product->is_in_stock) {
            return ['success' => false, 'message' => 'Product is out of stock.'];
        }

        if ($product->stock < $quantity) {
            return ['success' => false, 'message' => "Only {$product->stock} items available in stock."];
        }

        $cart = $this->getCart();
        $existingQuantity = (int) $cart->items()
            ->where('product_id', $product->id)
            ->value('quantity');

        if ($product->stock < ($existingQuantity + $quantity)) {
            return ['success' => false, 'message' => "Only {$product->stock} items available in stock."];
        }

        $cart->addItem($product, $quantity, $options);

        return ['success' => true, 'message' => 'Product added to cart successfully.'];
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $productId, int $quantity): array
    {
        $cart = $this->getCart();
        $item = $cart->items()->where('product_id', $productId)->first();

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found in cart.'];
        }

        $product = $item->product;

        if ($quantity <= 0) {
            $cart->removeItem($productId);
            return ['success' => true, 'message' => 'Item removed from cart.'];
        }

        if (!$product->is_in_stock || $product->stock < $quantity) {
            return ['success' => false, 'message' => 'Requested quantity not available.'];
        }

        $cart->updateItemQuantity($productId, $quantity);

        return ['success' => true, 'message' => 'Cart updated successfully.'];
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $productId): array
    {
        $cart = $this->getCart();

        if ($cart->removeItem($productId)) {
            return ['success' => true, 'message' => 'Item removed from cart.'];
        }

        return ['success' => false, 'message' => 'Item not found in cart.'];
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): array
    {
        $cart = $this->getCart();
        $cart->clear();

        return ['success' => true, 'message' => 'Cart cleared successfully.'];
    }

    /**
     * Get cart summary
     */
    public function getCartSummary(): array
    {
        $cart = $this->getCart();

        return [
            'items_count' => $cart->total_items,
            'subtotal' => $cart->subtotal,
            'tax_amount' => $cart->tax_amount,
            'shipping_amount' => $cart->shipping_amount,
            'discount_amount' => $cart->discount_amount,
            'total_price' => $cart->grand_total,
            'formatted_total' => $cart->formatted_total,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->formatted_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->formatted_subtotal,
                    'image' => $item->product->image ? asset('storage/' . $item->product->image) : null,
                    'available' => $item->isAvailable() && $item->hasAvailableQuantity(),
                ];
            }),
        ];
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(): array
    {
        $cart = $this->getCart();
        $errors = [];

        foreach ($cart->items as $item) {
            if (!$item->isAvailable()) {
                $errors[] = "{$item->product->name} is no longer available.";
            } elseif (!$item->hasAvailableQuantity()) {
                $errors[] = "Only {$item->product->stock} {$item->product->name}(s) available in stock.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'message' => implode(' ', $errors),
        ];
    }

    /**
     * Transfer cart to user when they log in
     */
    public function transferCartToUser(int $userId): void
    {
        $sessionId = Session::getId();

        // Find guest cart
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if ($guestCart) {
            // Check if user already has a cart
            $userCart = Cart::where('user_id', $userId)->first();

            if ($userCart) {
                // Merge guest cart items into user cart
                foreach ($guestCart->items as $item) {
                    $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();

                    if ($existingItem) {
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $item->quantity,
                        ]);
                    } else {
                        $item->update(['cart_id' => $userCart->id]);
                    }
                }

                // Delete guest cart
                $guestCart->delete();
            } else {
                // Associate guest cart with user
                $guestCart->update(['user_id' => $userId]);
            }
        }
    }
}

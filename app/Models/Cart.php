<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
    ];

    // Relationship with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with cart items
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Get total items count
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    // Get total price
    public function getTotalPriceAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getSubtotalAttribute(): float
    {
        return $this->total_price;
    }

    public function getTaxAmountAttribute(): float
    {
        $rate = config('shop.tax_rate', 0.08);
        return round($this->subtotal * $rate, 2);
    }

    public function getShippingAmountAttribute(): float
    {
        $threshold = config('shop.free_shipping_threshold', 100);
        $rate = config('shop.shipping_rate', 10.00);
        return $this->subtotal >= $threshold || $this->isEmpty() ? 0.00 : $rate;
    }

    public function getDiscountAmountAttribute(): float
    {
        $coupon = Session::get('coupon');

        if (! $coupon) {
            return 0.00;
        }

        return min((float) ($coupon['discount'] ?? 0), $this->subtotal);
    }

    public function getGrandTotalAttribute(): float
    {
        return max(0, $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount);
    }

    // Get formatted total price
    public function getFormattedTotalAttribute(): string
    {
        return taka($this->grand_total);
    }

    // Check if cart is empty
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    // Add item to cart
    public function addItem(Product $product, int $quantity = 1, array $options = []): CartItem
    {
        $existingItem = $this->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => $product->current_price,
            ]);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->current_price,
            'options' => $options,
        ]);
    }

    // Update item quantity
    public function updateItemQuantity(int $productId, int $quantity): bool
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item && $quantity > 0) {
            $item->update(['quantity' => $quantity]);
            return true;
        } elseif ($item && $quantity <= 0) {
            $item->delete();
            return true;
        }

        return false;
    }

    // Remove item from cart
    public function removeItem(int $productId): bool
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->delete();
            return true;
        }

        return false;
    }

    // Clear all items
    public function clear(): void
    {
        $this->items()->delete();
    }
}

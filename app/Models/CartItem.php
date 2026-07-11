<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
    ];

    // Relationship with cart
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // Relationship with product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Get subtotal for this item
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    // Get formatted subtotal
    public function getFormattedSubtotalAttribute(): string
    {
        return taka($this->subtotal);
    }

    // Get formatted price
    public function getFormattedPriceAttribute(): string
    {
        return taka($this->price);
    }

    // Check if product is still available
    public function isAvailable(): bool
    {
        return $this->product && $this->product->is_active && $this->product->is_in_stock;
    }

    // Check if requested quantity is available
    public function hasAvailableQuantity(): bool
    {
        return $this->product && $this->product->stock >= $this->quantity;
    }
}

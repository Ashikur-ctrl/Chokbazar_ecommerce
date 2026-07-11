<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FulfillmentRequestItem extends Model
{
    protected $fillable = [
        'fulfillment_request_id',
        'order_item_id',
        'product_id',
        'quantity',
        'price',
        'sku',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the fulfillment request
     */
    public function fulfillmentRequest(): BelongsTo
    {
        return $this->belongsTo(FulfillmentRequest::class);
    }

    /**
     * Get the order item
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

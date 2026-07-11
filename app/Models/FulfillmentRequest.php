<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FulfillmentRequest extends Model
{
    protected $fillable = [
        'order_id',
        'seller_id',
        'fulfillment_request_number',
        'status',
        'total_amount',
        'notes',
        'sent_at',
        'confirmed_at',
        'shipped_at',
        'tracking_number',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    /**
     * Get the order associated with this fulfillment request
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the seller associated with this fulfillment request
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get all items in this fulfillment request
     */
    public function items(): HasMany
    {
        return $this->hasMany(FulfillmentRequestItem::class);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed requests
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for shipped requests
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Generate fulfillment request number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->fulfillment_request_number) {
                $model->fulfillment_request_number = 'FR-' . strtoupper(substr(md5(time() . rand()), 0, 8));
            }
        });
    }
}

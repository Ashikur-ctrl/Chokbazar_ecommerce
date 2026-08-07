<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportProduct extends Model
{
    protected $fillable = [
        'import_batch_id',
        'seller_id',
        'source_offer_id',
        'raw_payload',
        'title_cn',
        'title_en',
        'description_cn',
        'description_en',
        'price_cny',
        'price_bdt',
        'fx_rate_used',
        'sku_data',
        'images',
        'status',
        'error_message',
        'product_id',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'sku_data' => 'array',
        'images' => 'array',
        'price_cny' => 'decimal:2',
        'price_bdt' => 'decimal:2',
        'fx_rate_used' => 'decimal:6',
        'reviewed_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Seller::class);
    }

    public function markFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $reason,
        ]);
        $this->batch->incrementFailed();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationAnalytics extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'type',
        'position',
        'source_product_id',
        'event',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function record(
        ?int $userId,
        ?string $sessionId,
        int $productId,
        string $type,
        string $position,
        ?string $sourceProductId = null,
        string $event = 'impression',
        array $metadata = []
    ): self {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId,
            'type' => $type,
            'position' => $position,
            'source_product_id' => $sourceProductId,
            'event' => $event,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function scopeImpressions($query)
    {
        return $query->where('event', 'impression');
    }

    public function scopeClicks($query)
    {
        return $query->where('event', 'click');
    }
}

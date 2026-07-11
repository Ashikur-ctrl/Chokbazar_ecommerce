<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBehavior extends Model
{
    public $timestamps = false; // We only need created_at

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'action',
        'weight',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user associated with this behavior
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with this behavior
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Record a user behavior
     */
    public static function record(
        int $userId = null,
        string $sessionId = null,
        int $productId,
        string $action,
        int $weight = 1,
        array $metadata = []
    ): self {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId,
            'action' => $action,
            'weight' => $weight,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * Get action weights for scoring
     */
    public static function getActionWeights(): array
    {
        return [
            'purchase' => 10,
            'add_to_cart' => 5,
            'wishlist' => 4,
            'view' => 1,
            'remove_from_cart' => -2,
        ];
    }

    /**
     * Scope for recent behaviors
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific user or session
     */
    public function scopeForUser($query, int $userId = null, string $sessionId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        if ($sessionId) {
            return $query->where('session_id', $sessionId);
        }
        return $query;
    }
}

<?php

namespace App\Services;

use App\Models\UserBehavior;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get collaborative filtering recommendations for a user
     * Based on "Users who bought this also bought" pattern
     */
    public function getCollaborativeRecommendations(
        int $userId = null,
        string $sessionId = null,
        int $productId = null,
        int $limit = 10
    ): Collection {
        // If we have a specific product, get recommendations based on that
        if ($productId) {
            return $this->getProductBasedRecommendations($productId, $userId, $sessionId, $limit);
        }

        // Otherwise, get general recommendations for the user
        return $this->getUserBasedRecommendations($userId, $sessionId, $limit);
    }

    /**
     * Get recommendations based on a specific product
     * "Customers who viewed/bought this also viewed/bought"
     */
    public function getProductBasedRecommendations(
        int $productId,
        int $userId = null,
        string $sessionId = null,
        int $limit = 10
    ): Collection {
        $cacheKey = "product_recs_{$productId}_{$userId}_{$sessionId}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $userId, $sessionId, $limit) {
            // Find users who interacted with this product
            $similarUserIds = UserBehavior::where('product_id', $productId)
                ->whereNotNull('user_id')
                ->where('user_id', '!=', $userId)
                ->pluck('user_id')
                ->unique()
                ->take(100); // Limit for performance

            if ($similarUserIds->isEmpty()) {
                return $this->getPopularProducts($limit);
            }

            // Find products that these similar users bought/viewed
            $recommendedProductIds = UserBehavior::whereIn('user_id', $similarUserIds)
                ->where('product_id', '!=', $productId)
                ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
                ->select('product_id', DB::raw('COUNT(*) as score'))
                ->groupBy('product_id')
                ->orderBy('score', 'desc')
                ->limit($limit * 2) // Get more to filter
                ->pluck('product_id');

            return Product::whereIn('id', $recommendedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->inRandomOrder() // Randomize order for variety
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get personalized recommendations for a user
     * Based on their behavior and similar users
     */
    public function getUserBasedRecommendations(
        int $userId = null,
        string $sessionId = null,
        int $limit = 10
    ): Collection {
        if (!$userId && !$sessionId) {
            return $this->getPopularProducts($limit);
        }

        $cacheKey = "user_recs_{$userId}_{$sessionId}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($userId, $sessionId, $limit) {
            // Get user's interacted products
            $userProductIds = UserBehavior::forUser($userId, $sessionId)
                ->pluck('product_id')
                ->unique();

            if ($userProductIds->isEmpty()) {
                return $this->getPopularProducts($limit);
            }

            // Find other users who interacted with similar products
            $similarUserIds = UserBehavior::whereIn('product_id', $userProductIds)
                ->whereNotNull('user_id')
                ->where('user_id', '!=', $userId)
                ->select('user_id', DB::raw('COUNT(*) as similarity_score'))
                ->groupBy('user_id')
                ->orderBy('similarity_score', 'desc')
                ->limit(50) // Top similar users
                ->pluck('user_id');

            if ($similarUserIds->isEmpty()) {
                return $this->getPopularProducts($limit);
            }

            // Get products that similar users interacted with but user hasn't
            $recommendedProductIds = UserBehavior::whereIn('user_id', $similarUserIds)
                ->whereNotIn('product_id', $userProductIds)
                ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
                ->select('product_id', DB::raw('COUNT(*) as recommendation_score'))
                ->groupBy('product_id')
                ->orderBy('recommendation_score', 'desc')
                ->limit($limit * 2)
                ->pluck('product_id');

            return Product::whereIn('id', $recommendedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get trending/popular products as fallback
     */
    public function getPopularProducts(int $limit = 10): Collection
    {
        return Cache::remember("popular_products_{$limit}", 3600, function () use ($limit) {
            $purchasedIds = UserBehavior::where('action', 'purchase')
                ->where('created_at', '>=', now()->subDays(30))
                ->select('product_id', DB::raw('COUNT(*) as purchase_count'))
                ->groupBy('product_id')
                ->orderBy('purchase_count', 'desc')
                ->limit($limit)
                ->pluck('product_id');

            if ($purchasedIds->isNotEmpty()) {
                $popular = Product::whereIn('id', $purchasedIds)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->get();

                if ($popular->isNotEmpty()) {
                    return $popular;
                }
            }

            // Fallback: newest active products
            return Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get frequently bought together products
     */
    public function getFrequentlyBoughtTogether(int $productId, int $limit = 5): Collection
    {
        $cacheKey = "fbt_{$productId}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            // Find orders that contain this product
            $orderIds = DB::table('order_items')
                ->where('product_id', $productId)
                ->pluck('order_id')
                ->unique();

            if ($orderIds->isEmpty()) {
                return collect();
            }

            // Find other products in those orders
            $relatedProductIds = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $productId)
                ->select('product_id', DB::raw('COUNT(*) as frequency'))
                ->groupBy('product_id')
                ->orderBy('frequency', 'desc')
                ->limit($limit)
                ->pluck('product_id');

            return Product::whereIn('id', $relatedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->get();
        });
    }

    /**
     * Record user behavior
     */
    public function recordBehavior(
        int $userId = null,
        string $sessionId = null,
        int $productId,
        string $action,
        array $metadata = []
    ): void {
        $weights = UserBehavior::getActionWeights();
        $weight = $weights[$action] ?? 1;

        UserBehavior::record($userId, $sessionId, $productId, $action, $weight, $metadata);

        // Clear related caches
        $this->clearRecommendationCaches($userId, $sessionId, $productId);
    }

    /**
     * Clear recommendation caches when behavior changes
     * Uses Cache::forget with up to 10 numeric suffixes to approximate wildcard deletion
     */
    private function clearRecommendationCaches(int $userId = null, string $sessionId = null, int $productId = null): void
    {
        // Clear known cache keys (wildcards don't work with file/database cache stores)
        $keys = [];

        // Product-based recs: product_recs_{productId}_{userId}_{sessionId}_{limit}
        for ($limit = 1; $limit <= 10; $limit++) {
            $keys[] = "product_recs_{$productId}_{$userId}_{$sessionId}_{$limit}";
            $keys[] = "user_recs_{$userId}_{$sessionId}_{$limit}";
        }

        // Popular products
        foreach ([10, 20] as $limit) {
            $keys[] = "popular_products_{$limit}";
        }

        // Frequently bought together
        foreach ([5, 10] as $limit) {
            $keys[] = "fbt_{$productId}_{$limit}";
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get user similarity score with another user
     */
    public function getUserSimilarity(int $userId1, int $userId2): float
    {
        $user1Products = UserBehavior::where('user_id', $userId1)
            ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
            ->pluck('product_id')
            ->unique();

        $user2Products = UserBehavior::where('user_id', $userId2)
            ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
            ->pluck('product_id')
            ->unique();

        if ($user1Products->isEmpty() || $user2Products->isEmpty()) {
            return 0.0;
        }

        $intersection = $user1Products->intersect($user2Products)->count();
        $union = $user1Products->merge($user2Products)->unique()->count();

        return $union > 0 ? $intersection / $union : 0.0; // Jaccard similarity
    }

    /**
     * Precompute and cache recommendations for active users
     */
    public function precomputeRecommendations(): void
    {
        // Get active users from recent behavior
        $activeUserIds = UserBehavior::recent(7)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->take(100); // Limit for performance

        foreach ($activeUserIds as $userId) {
            $this->getUserBasedRecommendations($userId, null, 20);
        }

        // Precompute popular products
        $this->getPopularProducts(20);
    }
}

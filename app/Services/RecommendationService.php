<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecommendationAnalytics;
use App\Models\UserBehavior;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    private function getActionWeights(): array
    {
        return UserBehavior::getActionWeights();
    }

    public function getCollaborativeRecommendations(
        ?int $userId,
        ?string $sessionId,
        ?int $productId,
        int $limit = 10
    ): Collection {
        if ($productId) {
            return $this->getProductBasedRecommendations($productId, $userId, $sessionId, $limit);
        }

        return $this->getUserBasedRecommendations($userId, $sessionId, $limit);
    }

    public function getProductBasedRecommendations(
        int $productId,
        ?int $userId,
        ?string $sessionId,
        int $limit = 10
    ): Collection {
        $cacheKey = "product_recs:{$productId}:".($userId ?? 'g').':'.($sessionId ?? 'g').":{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $userId, $limit) {
            $sourceProduct = Product::find($productId);

            $similarUserIds = UserBehavior::where('product_id', $productId)
                ->where('user_id', '!=', $userId)
                ->select('user_id', DB::raw('SUM(weight) as similarity'))
                ->groupBy('user_id')
                ->orderByDesc('similarity')
                ->limit(100)
                ->pluck('user_id');

            if ($similarUserIds->isEmpty()) {
                return $this->getContentBasedRecommendations($sourceProduct, $limit);
            }

            $recommendedProductIds = UserBehavior::whereIn('user_id', $similarUserIds)
                ->where('product_id', '!=', $productId)
                ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
                ->select('product_id', DB::raw('SUM(weight) as score'))
                ->groupBy('product_id')
                ->orderByDesc('score')
                ->limit($limit * 3)
                ->pluck('product_id');

            if ($recommendedProductIds->isEmpty()) {
                return $this->getContentBasedRecommendations($sourceProduct, $limit);
            }

            $products = Product::whereIn('id', $recommendedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'primaryImage'])
                ->get()
                ->sortByDesc(fn ($p) => $recommendedProductIds->search($p->id))
                ->values();

            return $this->addDiversity($products, $limit);
        });
    }

    public function getUserBasedRecommendations(
        ?int $userId,
        ?string $sessionId,
        int $limit = 10
    ): Collection {
        if (! $userId && ! $sessionId) {
            return $this->getPopularProducts($limit);
        }

        $cacheKey = 'user_recs:'.($userId ?? 'g').':'.($sessionId ?? 'g').":{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($userId, $sessionId, $limit) {
            $userProductIds = UserBehavior::forUser($userId, $sessionId)
                ->whereIn('action', ['view', 'add_to_cart', 'purchase', 'wishlist'])
                ->pluck('product_id')
                ->unique();

            if ($userProductIds->isEmpty()) {
                return $this->getPopularProducts($limit);
            }

            $negativeProductIds = UserBehavior::forUser($userId, $sessionId)
                ->where('action', 'remove_from_cart')
                ->pluck('product_id')
                ->unique();

            $similarUserIds = UserBehavior::whereIn('product_id', $userProductIds)
                ->where('user_id', '!=', $userId)
                ->whereNotNull('user_id')
                ->select('user_id', DB::raw('SUM(weight) as similarity'))
                ->groupBy('user_id')
                ->orderByDesc('similarity')
                ->limit(50)
                ->pluck('user_id');

            if ($similarUserIds->isEmpty()) {
                return $this->getContentBasedRecommendations(null, $limit, $userProductIds);
            }

            $recommendedProductIds = UserBehavior::whereIn('user_id', $similarUserIds)
                ->whereNotIn('product_id', $userProductIds)
                ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
                ->select('product_id', DB::raw('SUM(weight) as score'))
                ->groupBy('product_id')
                ->orderByDesc('score')
                ->limit($limit * 3)
                ->pluck('product_id');

            if ($recommendedProductIds->isEmpty()) {
                return $this->getContentBasedRecommendations(null, $limit, $userProductIds);
            }

            $products = Product::whereIn('id', $recommendedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'primaryImage'])
                ->get()
                ->reject(fn ($p) => $negativeProductIds->contains($p->id))
                ->sortByDesc(fn ($p) => $recommendedProductIds->search($p->id))
                ->values();

            return $this->addDiversity($products, $limit);
        });
    }

    public function getPopularProducts(int $limit = 10): Collection
    {
        $cacheKey = "popular_products:{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            $purchasedIds = UserBehavior::where('action', 'purchase')
                ->where('created_at', '>=', now()->subDays(30))
                ->select('product_id', DB::raw('SUM(weight) as purchase_count'))
                ->groupBy('product_id')
                ->orderByDesc('purchase_count')
                ->limit($limit)
                ->pluck('product_id');

            if ($purchasedIds->isNotEmpty()) {
                $popular = Product::whereIn('id', $purchasedIds)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->with(['category', 'primaryImage'])
                    ->get()
                    ->sortByDesc(fn ($p) => $purchasedIds->search($p->id))
                    ->values();

                if ($popular->isNotEmpty()) {
                    return $popular;
                }
            }

            return Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'primaryImage'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    public function getFrequentlyBoughtTogether(int $productId, int $limit = 5): Collection
    {
        $cacheKey = "fbt:{$productId}:{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            $orderIds = DB::table('order_items')
                ->where('product_id', $productId)
                ->pluck('order_id')
                ->unique();

            if ($orderIds->isEmpty()) {
                return collect();
            }

            $relatedProductIds = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $productId)
                ->select('product_id', DB::raw('COUNT(*) as frequency'))
                ->groupBy('product_id')
                ->orderByDesc('frequency')
                ->limit($limit * 2)
                ->pluck('product_id');

            return Product::whereIn('id', $relatedProductIds)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'primaryImage'])
                ->get()
                ->sortByDesc(fn ($p) => $relatedProductIds->search($p->id))
                ->values()
                ->take($limit);
        });
    }

    private function getContentBasedRecommendations(
        ?Product $sourceProduct,
        int $limit,
        ?Collection $excludeProductIds = null
    ): Collection {
        $query = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['category', 'primaryImage']);

        if ($sourceProduct) {
            $query->where(function ($q) use ($sourceProduct) {
                $q->where('category_id', $sourceProduct->category_id)
                    ->orWhere('seller_id', $sourceProduct->seller_id);
            });
        } elseif ($excludeProductIds && $excludeProductIds->isNotEmpty()) {
            $related = UserBehavior::whereIn('product_id', $excludeProductIds)
                ->whereIn('action', ['purchase', 'add_to_cart', 'wishlist'])
                ->pluck('product_id')
                ->unique();

            if ($related->isNotEmpty()) {
                $query->whereIn('id', $related);
            }
        }

        return $query->latest()->limit($limit * 2)->get()->take($limit);
    }

    private function addDiversity(Collection $products, int $limit): Collection
    {
        $selected = [];
        $categoryCounts = [];

        foreach ($products as $product) {
            $categoryId = $product->category_id ?? 'uncategorized';
            $categoryCounts[$categoryId] = ($categoryCounts[$categoryId] ?? 0) + 1;

            if (count($selected) >= $limit) {
                break;
            }

            if ($categoryCounts[$categoryId] <= ceil($limit / 3)) {
                $selected[] = $product;
            }
        }

        while (count($selected) < $limit && $products->isNotEmpty()) {
            $remaining = $products->diff($selected);
            if ($remaining->isEmpty()) {
                break;
            }
            $selected[] = $remaining->first();
        }

        return collect($selected);
    }

    public function recordBehavior(
        ?int $userId,
        ?string $sessionId,
        int $productId,
        string $action,
        array $metadata = []
    ): void {
        $weights = $this->getActionWeights();
        $weight = $weights[$action] ?? 1;

        UserBehavior::record($userId, $sessionId, $productId, $action, $weight, $metadata);

        $this->clearRecommendationCaches($userId, $sessionId, $productId);
    }

    public function recordImpression(
        ?int $userId,
        ?string $sessionId,
        int $productId,
        string $type,
        string $position,
        ?string $sourceProductId = null,
        array $metadata = []
    ): void {
        RecommendationAnalytics::record(
            $userId,
            $sessionId,
            $productId,
            $type,
            $position,
            $sourceProductId,
            'impression',
            $metadata
        );
    }

    public function recordClick(
        ?int $userId,
        ?string $sessionId,
        int $productId,
        string $type,
        string $position,
        ?string $sourceProductId = null,
        array $metadata = []
    ): void {
        RecommendationAnalytics::record(
            $userId,
            $sessionId,
            $productId,
            $type,
            $position,
            $sourceProductId,
            'click',
            $metadata
        );
    }

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

        return $union > 0 ? $intersection / $union : 0.0;
    }

    public function precomputeRecommendations(): void
    {
        $activeUserIds = UserBehavior::recent(7)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->take(100);

        foreach ($activeUserIds as $userId) {
            $this->getUserBasedRecommendations($userId, null, 20);
        }

        $this->getPopularProducts(20);
    }

    private function clearRecommendationCaches(?int $userId, ?string $sessionId, ?int $productId): void
    {
        $keys = [];

        $prefixes = [
            "product_recs:{$productId}:",
            'user_recs:',
        ];

        foreach (range(1, 10) as $limit) {
            $keys[] = "product_recs:{$productId}:".($userId ?? 'g').':'.($sessionId ?? 'g').":{$limit}";
            $keys[] = 'user_recs:'.($userId ?? 'g').':'.($sessionId ?? 'g').":{$limit}";
        }

        foreach ([10, 20] as $limit) {
            $keys[] = "popular_products:{$limit}";
        }

        foreach ([5, 10] as $limit) {
            $keys[] = "fbt:{$productId}:{$limit}";
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}

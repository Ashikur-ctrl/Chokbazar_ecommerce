<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    protected RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get personalized recommendations for current user
     */
    public function personalized(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = $request->session()->getId();
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getUserBasedRecommendations(
            $userId,
            $sessionId,
            $limit
        );

        return response()->json([
            'recommendations' => $recommendations->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                    'category' => $product->category?->name,
                ];
            }),
            'type' => 'personalized',
        ]);
    }

    /**
     * Get product-based recommendations
     */
    public function productBased(Request $request, int $productId): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = $request->session()->getId();
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getProductBasedRecommendations(
            $productId,
            $userId,
            $sessionId,
            $limit
        );

        return response()->json([
            'recommendations' => $recommendations->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                    'category' => $product->category?->name,
                ];
            }),
            'type' => 'product_based',
            'based_on_product_id' => $productId,
        ]);
    }

    /**
     * Get frequently bought together products
     */
    public function frequentlyBoughtTogether(Request $request, int $productId): JsonResponse
    {
        $limit = $request->get('limit', 5);

        $recommendations = $this->recommendationService->getFrequentlyBoughtTogether(
            $productId,
            $limit
        );

        return response()->json([
            'recommendations' => $recommendations->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                ];
            }),
            'type' => 'frequently_bought_together',
            'based_on_product_id' => $productId,
        ]);
    }

    /**
     * Get popular/trending products
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getPopularProducts($limit);

        return response()->json([
            'recommendations' => $recommendations->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                    'category' => $product->category?->name,
                ];
            }),
            'type' => 'popular',
        ]);
    }

    /**
     * Record user behavior (for analytics)
     */
    public function recordBehavior(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:view,add_to_cart,purchase,wishlist,remove_from_cart',
            'metadata' => 'nullable|array',
        ]);

        $userId = auth()->id();
        $sessionId = $request->session()->getId();

        $this->recommendationService->recordBehavior(
            $userId,
            $sessionId,
            $validated['product_id'],
            $validated['action'],
            $validated['metadata'] ?? []
        );

        return response()->json(['success' => true]);
    }
}

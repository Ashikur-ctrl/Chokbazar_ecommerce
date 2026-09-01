<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Models\RecommendationAnalytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function __construct(protected RecommendationService $recommendationService) {}

    public function personalized(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');
        $limit = (int) $request->get('limit', 10);
        $position = $request->get('position', 'home_page');

        $recommendations = $this->recommendationService->getUserBasedRecommendations(
            $userId,
            $sessionId,
            $limit
        );

        $this->recordImpressions($userId, $sessionId, $recommendations, 'personalized', $position);

        return response()->json([
            'recommendations' => $this->mapProducts($recommendations),
            'type' => 'personalized',
        ]);
    }

    public function productBased(Request $request, int $productId): JsonResponse
    {
        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');
        $limit = (int) $request->get('limit', 10);
        $position = $request->get('position', 'product_page');

        $recommendations = $this->recommendationService->getProductBasedRecommendations(
            $productId,
            $userId,
            $sessionId,
            $limit
        );

        $this->recordImpressions($userId, $sessionId, $recommendations, 'product_based', $position, (string) $productId);

        return response()->json([
            'recommendations' => $this->mapProducts($recommendations),
            'type' => 'product_based',
            'based_on_product_id' => $productId,
        ]);
    }

    public function frequentlyBoughtTogether(Request $request, int $productId): JsonResponse
    {
        $limit = (int) $request->get('limit', 5);
        $position = $request->get('position', 'product_page');

        $recommendations = $this->recommendationService->getFrequentlyBoughtTogether(
            $productId,
            $limit
        );

        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');

        $this->recordImpressions($userId, $sessionId, $recommendations, 'frequently_bought_together', $position, (string) $productId);

        return response()->json([
            'recommendations' => $this->mapProducts($recommendations),
            'type' => 'frequently_bought_together',
            'based_on_product_id' => $productId,
        ]);
    }

    public function popular(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 10);
        $position = $request->get('position', 'home_page');

        $recommendations = $this->recommendationService->getPopularProducts($limit);

        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');

        $this->recordImpressions($userId, $sessionId, $recommendations, 'popular', $position);

        return response()->json([
            'recommendations' => $this->mapProducts($recommendations),
            'type' => 'popular',
        ]);
    }

    public function recordBehavior(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:view,add_to_cart,purchase,wishlist,remove_from_cart',
            'metadata' => 'nullable|array',
        ]);

        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');

        $this->recommendationService->recordBehavior(
            $userId,
            $sessionId,
            $validated['product_id'],
            $validated['action'],
            $validated['metadata'] ?? []
        );

        return response()->json(['success' => true]);
    }

    public function recordClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|string|max:50',
            'position' => 'required|string|max:50',
            'source_product_id' => 'nullable|string|max:50',
            'metadata' => 'nullable|array',
        ]);

        $userId = auth()->id();
        $sessionId = $request->hasSession() ? $request->session()->getId() : ($request->header('X-Session-ID') ?? 'api-guest');

        $this->recommendationService->recordClick(
            $userId,
            $sessionId,
            $validated['product_id'],
            $validated['type'],
            $validated['position'],
            $validated['source_product_id'] ?? null,
            $validated['metadata'] ?? []
        );

        return response()->json(['success' => true]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $totalBehaviors = (int) DB::table('user_behaviors')->count();
        $uniqueUsers = (int) DB::table('user_behaviors')->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $uniqueProducts = (int) DB::table('user_behaviors')->distinct('product_id')->count('product_id');

        return response()->json([
            'total_behaviors' => $totalBehaviors,
            'unique_users' => $uniqueUsers,
            'unique_products' => $uniqueProducts,
        ]);
    }

    private function mapProducts(Collection $products): array
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->current_price,
                'sale_price' => $product->sale_price,
                'image' => $product->image_url ?? $product->image,
                'is_on_sale' => $product->is_on_sale,
                'discount_percentage' => $product->discount_percentage,
                'category' => $product->category?->name,
            ];
        })->all();
    }

    private function recordImpressions(
        ?int $userId,
        ?string $sessionId,
        Collection $products,
        string $type,
        string $position,
        ?string $sourceProductId = null
    ): void {
        foreach ($products as $index => $product) {
            $this->recommendationService->recordImpression(
                $userId,
                $sessionId,
                $product->id,
                $type,
                $position,
                $sourceProductId,
                ['position' => $index + 1]
            );
        }
    }
}

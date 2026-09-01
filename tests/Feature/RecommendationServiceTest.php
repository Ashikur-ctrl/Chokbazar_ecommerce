<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\UserBehavior;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RecommendationService::class);
    }

    public function test_get_popular_products_returns_active_in_stock_products(): void
    {
        $activeProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $inactiveProduct = Product::factory()->create(['is_active' => false, 'stock' => 10]);
        $outOfStockProduct = Product::factory()->create(['is_active' => true, 'stock' => 0]);

        $products = $this->service->getPopularProducts(10);

        $productIds = $products->pluck('id');
        $this->assertContains($activeProduct->id, $productIds);
        $this->assertNotContains($inactiveProduct->id, $productIds);
        $this->assertNotContains($outOfStockProduct->id, $productIds);
    }

    public function test_product_based_recommendations_uses_content_fallback_when_no_similar_users(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $categoryProduct = Product::factory()->create(['category_id' => $product->category_id, 'is_active' => true, 'stock' => 10]);

        $recommendations = $this->service->getProductBasedRecommendations($product->id, null, null, 5);

        $this->assertContains($categoryProduct->id, $recommendations->pluck('id'));
    }

    public function test_user_based_recommendations_returns_popular_for_new_user(): void
    {
        $popularProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);

        UserBehavior::create([
            'user_id' => null,
            'session_id' => 'new-session',
            'product_id' => $popularProduct->id,
            'action' => 'purchase',
            'weight' => 10,
            'created_at' => now(),
        ]);

        $recommendations = $this->service->getUserBasedRecommendations(null, 'new-session', 5);

        $this->assertTrue($recommendations->isNotEmpty());
    }

    public function test_negative_feedback_excludes_products(): void
    {
        $user = User::factory()->create();
        $likedProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $dislikedProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $otherProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);

        UserBehavior::create(['user_id' => $user->id, 'product_id' => $likedProduct->id, 'action' => 'wishlist', 'weight' => 4, 'created_at' => now()]);
        UserBehavior::create(['user_id' => $user->id, 'product_id' => $dislikedProduct->id, 'action' => 'remove_from_cart', 'weight' => -2, 'created_at' => now()]);

        $otherUser = User::factory()->create();
        UserBehavior::create(['user_id' => $otherUser->id, 'product_id' => $likedProduct->id, 'action' => 'purchase', 'weight' => 10, 'created_at' => now()]);
        UserBehavior::create(['user_id' => $otherUser->id, 'product_id' => $dislikedProduct->id, 'action' => 'purchase', 'weight' => 10, 'created_at' => now()]);
        UserBehavior::create(['user_id' => $otherUser->id, 'product_id' => $otherProduct->id, 'action' => 'purchase', 'weight' => 10, 'created_at' => now()]);

        $recommendations = $this->service->getUserBasedRecommendations($user->id, null, 10);

        $this->assertNotContains($dislikedProduct->id, $recommendations->pluck('id'));
    }

    public function test_frequently_bought_together_returns_related_products(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'stock' => 10]);
        $relatedProduct = Product::factory()->create(['is_active' => true, 'stock' => 10]);

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '01700000000',
            'shipping_address' => 'Test Address',
            'order_number' => 'ORD-'.Str::random(8),
            'invoice_number' => 'INV-'.date('Ymd').'-'.Str::random(5),
            'subtotal' => 100,
            'total_amount' => 100,
        ]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'price' => $product->price, 'quantity' => 1, 'total' => $product->price]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $relatedProduct->id, 'product_name' => $relatedProduct->name, 'price' => $relatedProduct->price, 'quantity' => 1, 'total' => $relatedProduct->price]);

        $recommendations = $this->service->getFrequentlyBoughtTogether($product->id, 5);

        $this->assertContains($relatedProduct->id, $recommendations->pluck('id'));
    }

    public function test_record_behavior_creates_user_behavior(): void
    {
        $product = Product::factory()->create();

        $this->service->recordBehavior(null, 'test-session', $product->id, 'view');

        $this->assertDatabaseHas('user_behaviors', [
            'session_id' => 'test-session',
            'product_id' => $product->id,
            'action' => 'view',
        ]);
    }
}

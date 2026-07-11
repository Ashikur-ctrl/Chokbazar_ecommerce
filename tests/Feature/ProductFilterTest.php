<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_api_returns_products()
    {
        // Create products via factory
        $p1 = Product::factory()->create(['name' => 'Test Product 1', 'price' => 100, 'stock' => 10]);
        $p2 = Product::factory()->create(['name' => 'Another Product', 'price' => 200, 'stock' => 5]);

        $response = $this->getJson('/api/products/filter');

        $response->assertStatus(200)
                 ->assertJsonStructure(['products', 'meta']);

        $this->assertGreaterThanOrEqual(2, count($response->json('products')));
    }
}

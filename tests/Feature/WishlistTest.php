<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_toggle_wishlist_json()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create(['name' => 'Wish Product', 'price' => 50, 'stock' => 3]);

        $response = $this->actingAs($user)->postJson('/wishlist/'.$product->slug);
        $response->assertStatus(200)->assertJson(['success' => true, 'added' => true]);

        // Toggling again should remove
        $response2 = $this->actingAs($user)->postJson('/wishlist/'.$product->slug);
        $response2->assertStatus(200)->assertJson(['success' => true, 'removed' => true]);
    }
}

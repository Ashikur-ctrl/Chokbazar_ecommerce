<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Seller;
use App\Models\FulfillmentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_web_routes_render_without_500_errors()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $sellerUser = User::factory()->create(['role' => 'seller']);
        $seller = Seller::create([
            'name' => 'Test Seller',
            'email' => 'seller@example.com',
            'phone' => '01700000000',
            'company_name' => 'Test Company',
            'api_key' => 'test-api-key',
            'fulfillment_method' => 'email',
            'is_active' => true,
        ]);
        $sellerUser->update(['seller_id' => $seller->id]);

        $product = Product::factory()->create(['seller_id' => $seller->id]);
        $order = Order::create([
            'order_number' => 'ORD-1001',
            'user_id' => $admin->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '01700000000',
            'shipping_address' => 'Dhaka, Bangladesh',
            'subtotal' => 100,
            'total_amount' => 100,
            'currency' => 'BDT',
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        
        $fulfillment = FulfillmentRequest::create([
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'request_number' => 'FR-1001',
            'total_amount' => 100,
            'status' => 'pending',
            'notes' => 'Test fulfillment request',
        ]);

        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn($r) => in_array('GET', $r->methods()))
            ->reject(fn($r) => str_starts_with($r->uri(), '_') || str_starts_with($r->uri(), 'sanctum') || str_contains($r->uri(), '{path}') || str_contains($r->uri(), 'livewire') || str_contains($r->uri(), 'filament'))
            ->values();

        $errors = [];

        foreach ($routes as $r) {
            $uri = $r->uri();

            // Replace parameters with real sample model IDs
            $testUri = str_replace(
                ['{product}', '{order}', '{seller}', '{user}', '{mode}', '{token}', '{id}', '{hash}', '{fulfillmentRequest}'],
                [$product->id, $order->id, $seller->id, $admin->id, 'local', 'test-token', $admin->id, 'test-hash', $fulfillment->id],
                $uri
            );

            // Determine appropriate user for auth test
            $authUser = $admin;
            if (str_starts_with($uri, 'seller/')) {
                $authUser = $sellerUser;
            }

            $headers = str_starts_with($testUri, 'api/') ? ['Accept' => 'application/json'] : [];

            // Test as Guest
            $responseGuest = $this->withHeaders($headers)->get('/' . ltrim($testUri, '/'));
            if ($responseGuest->status() >= 500) {
                $errors[] = "[GUEST FAIL {$responseGuest->status()}] /{$testUri}";
            }

            // Test as Authenticated User
            $responseAuth = $this->withHeaders($headers)->actingAs($authUser)->get('/' . ltrim($testUri, '/'));
            if ($responseAuth->status() >= 500) {
                $errors[] = "[AUTH FAIL {$responseAuth->status()}] /{$testUri}";
            }
        }

        if (!empty($errors)) {
            $this->fail("The following routes threw 500 Server Errors:\n" . implode("\n", $errors));
        }

        $this->assertTrue(true);
    }
}

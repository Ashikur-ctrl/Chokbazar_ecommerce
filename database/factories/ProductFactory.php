<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'sale_price' => null,
            'cost_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 5,
            'sku' => strtoupper(Str::random(8)),
            'tags' => [],
            'image' => null,
            'seo_title' => null,
            'seo_description' => null,
            'is_featured' => false,
            'is_active' => true,
            'visibility_status' => 'active',
            'category_id' => null,
            'seller_id' => null,
        ];
    }
}

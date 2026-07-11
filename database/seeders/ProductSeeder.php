<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'slug' => 'wireless-bluetooth-headphones',
                'description' => 'High-quality wireless Bluetooth headphones with noise cancellation and 30-hour battery life. Perfect for music lovers and professionals.',
                'short_description' => 'Premium wireless headphones with noise cancellation',
                'price' => 199.99,
                'sale_price' => 149.99,
                'stock' => 50,
                'sku' => 'WBH-001',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => 1, // Electronics
            ],
            [
                'name' => 'Smart Watch Series 5',
                'slug' => 'smart-watch-series-5',
                'description' => 'Advanced smartwatch with health monitoring, GPS, and cellular connectivity. Track your fitness, receive notifications, and stay connected.',
                'short_description' => 'Advanced smartwatch with health tracking',
                'price' => 399.99,
                'stock' => 25,
                'sku' => 'SW-005',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => 1, // Electronics
            ],
            [
                'name' => 'Cotton T-Shirt',
                'slug' => 'cotton-t-shirt',
                'description' => 'Comfortable 100% cotton t-shirt available in multiple colors. Perfect for casual wear and everyday comfort.',
                'short_description' => 'Comfortable 100% cotton t-shirt',
                'price' => 19.99,
                'sale_price' => 14.99,
                'stock' => 100,
                'sku' => 'TS-001',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => 2, // Clothing
            ],
            [
                'name' => 'The Art of Programming',
                'slug' => 'the-art-of-programming',
                'description' => 'Comprehensive guide to programming fundamentals and best practices. Essential reading for developers at all levels.',
                'short_description' => 'Essential programming guide for developers',
                'price' => 49.99,
                'stock' => 75,
                'sku' => 'BK-001',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => 3, // Books
            ],
            [
                'name' => 'Garden Hose 50ft',
                'slug' => 'garden-hose-50ft',
                'description' => 'Durable 50-foot garden hose with brass connectors. Perfect for watering your lawn and garden efficiently.',
                'short_description' => 'Durable 50ft garden hose with brass connectors',
                'price' => 29.99,
                'stock' => 40,
                'sku' => 'GH-050',
                'is_featured' => false,
                'is_active' => true,
                'category_id' => 4, // Home & Garden
            ],
            [
                'name' => 'Yoga Mat Premium',
                'slug' => 'yoga-mat-premium',
                'description' => 'High-quality, non-slip yoga mat made from eco-friendly materials. Perfect for yoga, pilates, and other floor exercises.',
                'short_description' => 'Non-slip eco-friendly yoga mat',
                'price' => 39.99,
                'sale_price' => 34.99,
                'stock' => 60,
                'sku' => 'YM-001',
                'is_featured' => true,
                'is_active' => true,
                'category_id' => 5, // Sports & Outdoors
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}

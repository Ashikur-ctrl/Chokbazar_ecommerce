<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DarazProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first() ?? Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $clothing = Category::where('name', 'Clothing')->first() ?? Category::create(['name' => 'Clothing', 'slug' => 'clothing']);
        $books = Category::where('name', 'Books')->first() ?? Category::create(['name' => 'Books', 'slug' => 'books']);
        $home = Category::where('name', 'Home & Garden')->first() ?? Category::create(['name' => 'Home & Garden', 'slug' => 'home-garden']);
        $sports = Category::where('name', 'Sports & Outdoors')->first() ?? Category::create(['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors']);

        $products = [
            [
                'name' => 'Walton Airbuds Pro TWS Wireless Earphones with ANC',
                'category_id' => $electronics->id,
                'price' => 2490.00,
                'sale_price' => 1890.00,
                'cost_price' => 1400.00,
                'stock' => 45,
                'sku' => 'DRZ-WLT-001',
                'short_description' => 'Bluetooth 5.3 Active Noise Cancelling TWS with 28-hour total battery backup and IPX5 water resistance.',
                'description' => "Experience crystal clear audio with Walton Airbuds Pro TWS. Featuring Active Noise Cancellation (ANC) up to 25dB, low-latency gaming mode, 10mm dynamic drivers, and intuitive touch controls. Designed for everyday comfort and Bangladesh weather with IPX5 sweat resistance.",
                'image' => 'products/walton-airbuds.svg',
                'is_featured' => true,
                'is_active' => true,
                'sourcing_type' => 'both',
                'visibility_status' => 'published',
                'tags' => ['electronics', 'tws', 'audio', 'trending'],
            ],
            [
                'name' => 'Realme 12+ 5G Smartphone (8GB RAM / 256GB Storage)',
                'category_id' => $electronics->id,
                'price' => 32999.00,
                'sale_price' => 29499.00,
                'cost_price' => 26000.00,
                'stock' => 20,
                'sku' => 'DRZ-RLM-002',
                'short_description' => '50MP Sony LYT-600 OIS Camera, Dimensity 7050 5G Processor, 67W SUPERVOOC Charge.',
                'description' => "The Realme 12+ 5G offers flagship-grade photography with the Sony LYT-600 OIS main sensor. Features a 120Hz Ultra Smooth AMOLED display, 5000mAh battery with 67W flash charging, and a luxury watch-inspired vegan leather back panel design.",
                'image' => 'products/realme-12plus.svg',
                'is_featured' => true,
                'is_active' => true,
                'sourcing_type' => 'both',
                'visibility_status' => 'published',
                'tags' => ['electronics', 'mobile', 'smartphone', 'featured'],
            ],
            [
                'name' => "Apex Men's Genuine Leather Casual Loafer Shoes - Black",
                'category_id' => $clothing->id,
                'price' => 4990.00,
                'sale_price' => 3890.00,
                'cost_price' => 2800.00,
                'stock' => 30,
                'sku' => 'DRZ-APX-003',
                'short_description' => 'Premium Bangladesh genuine leather upper, cushioned memory foam footbed, durable rubber sole.',
                'description' => "Crafted from 100% authentic local Bangladesh full-grain leather. Apex loafers deliver timeless elegance with maximum comfort for formal occasions and daily office wear. Breathable inner lining with non-slip TPR outsole.",
                'image' => 'products/apex-loafers.svg',
                'is_featured' => false,
                'is_active' => true,
                'visibility_status' => 'published',
                'tags' => ['clothing', 'shoes', 'fashion', 'men'],
            ],
            [
                'name' => 'Miyako 1.8L Non-Stick Electric Rice Cooker (MRC-188)',
                'category_id' => $home->id,
                'price' => 3600.00,
                'sale_price' => 2850.00,
                'cost_price' => 2100.00,
                'stock' => 50,
                'sku' => 'DRZ-MYK-004',
                'short_description' => 'Dual pot design with automatic keep-warm function, steamer tray, and energy-efficient 700W heating coil.',
                'description' => "Make fluffy, delicious rice effortless with the Miyako 1.8L Rice Cooker. Includes a heavy-duty non-stick inner pot, food-grade aluminum steamer basket, measuring cup, and automatic warm indicator. Perfect for Bangladeshi family dining.",
                'image' => 'products/miyako-rice-cooker.svg',
                'is_featured' => true,
                'is_active' => true,
                'sourcing_type' => 'both',
                'visibility_status' => 'published',
                'tags' => ['home', 'appliance', 'kitchen'],
            ],
            [
                'name' => 'Aarong Earth Organic Neem & Turmeric Face Wash (150ml)',
                'category_id' => $home->id,
                'price' => 450.00,
                'sale_price' => 380.00,
                'cost_price' => 250.00,
                'stock' => 100,
                'sku' => 'DRZ-ARG-005',
                'short_description' => '100% natural herbal formula for deep pore cleansing, acne protection, and bright skin tone.',
                'description' => "Formulated with pure extracts of organic neem leaves and wild turmeric. Aarong Earth Face Wash gently cleanses oil and impurities without drying out skin, leaving a fresh and radiant complexion.",
                'image' => 'products/aarong-facewash.svg',
                'is_featured' => false,
                'is_active' => true,
                'visibility_status' => 'published',
                'tags' => ['beauty', 'skincare', 'organic'],
            ],
            [
                'name' => 'Xiaomi Smart Band 8 Fitness Tracker - Graphite Black',
                'category_id' => $sports->id,
                'price' => 4800.00,
                'sale_price' => 3750.00,
                'cost_price' => 2900.00,
                'stock' => 35,
                'sku' => 'DRZ-XIA-006',
                'short_description' => '1.62" AMOLED 60Hz display, 150+ workout modes, SpO2 & heart rate monitoring, 16-day battery life.',
                'description' => "Stay active and healthy with Xiaomi Smart Band 8. Features an upgraded quick-release strap design, 60Hz high refresh rate AMOLED display, automatic brightness adjustment, continuous heart rate and sleep tracking, and 5ATM water resistance.",
                'image' => 'products/xiaomi-band8.svg',
                'is_featured' => true,
                'is_active' => true,
                'sourcing_type' => 'both',
                'visibility_status' => 'published',
                'tags' => ['sports', 'gadgets', 'fitness', 'trending'],
            ],
            [
                'name' => 'Symphony Z70 (4GB RAM / 64GB ROM) Dual SIM',
                'category_id' => $electronics->id,
                'price' => 9499.00,
                'sale_price' => 8690.00,
                'cost_price' => 7200.00,
                'stock' => 25,
                'sku' => 'DRZ-SYM-007',
                'short_description' => '6.56" HD+ 90Hz punch-hole display, 5000mAh long-life battery, 13MP AI dual rear camera.',
                'description' => "Affordable performance tailored for Bangladesh users. Symphony Z70 features Android 13, 90Hz smooth punch-hole display, side-mounted fingerprint sensor, type-C fast charging, and expandable memory up to 256GB.",
                'image' => 'products/symphony-z70.svg',
                'is_featured' => false,
                'is_active' => true,
                'visibility_status' => 'published',
                'tags' => ['electronics', 'mobile', 'budget'],
            ],
            [
                'name' => 'Humayun Ahmed Selected Novel Collection (Hardcover Set)',
                'category_id' => $books->id,
                'price' => 1800.00,
                'sale_price' => 1450.00,
                'cost_price' => 950.00,
                'stock' => 40,
                'sku' => 'DRZ-BOK-008',
                'short_description' => 'Deluxe edition set featuring Misir Ali and Himu classic novels in high quality hardcover.',
                'description' => "A timeless collection of iconic Bengali literature by master storyteller Humayun Ahmed. Printed on premium 80GSM cream paper with elegant gold foil hardcover embossing. Includes 5 top rated titles.",
                'image' => 'products/humayun-novels.svg',
                'is_featured' => false,
                'is_active' => true,
                'visibility_status' => 'published',
                'tags' => ['books', 'novels', 'bestseller'],
            ],
            [
                'name' => 'Singer 1.5 Ton Inverter AC (3 Star Energy Saver)',
                'category_id' => $home->id,
                'price' => 58900.00,
                'sale_price' => 51490.00,
                'cost_price' => 44000.00,
                'stock' => 12,
                'sku' => 'DRZ-SNG-009',
                'short_description' => '100% copper condenser coil, HD dust filter, R32 eco-friendly refrigerant with 10-year compressor warranty.',
                'description' => "Beat the summer heat with Singer 1.5 Ton Inverter Split Air Conditioner. Engineered with Gold Fin anti-corrosion technology, rapid cooling 18000 BTU, low power consumption inverter motor, and whisper-quiet indoor unit.",
                'image' => 'products/singer-ac.svg',
                'is_featured' => true,
                'is_active' => true,
                'sourcing_type' => 'both',
                'visibility_status' => 'published',
                'tags' => ['home', 'appliance', 'ac'],
            ],
            [
                'name' => 'Vector X Match Pro Leather Football (Size 5)',
                'category_id' => $sports->id,
                'price' => 1650.00,
                'sale_price' => 1250.00,
                'cost_price' => 800.00,
                'stock' => 60,
                'sku' => 'DRZ-VCT-010',
                'short_description' => 'Hand-stitched PU synthetic leather match ball approved for turf and grass play with latex bladder.',
                'description' => "Professional size 5 football built for high impact matches. Crafted with 32 panels of durable waterproof PU leather, latex inner bladder for superior air retention, and balanced flight stability.",
                'image' => 'products/vector-football.svg',
                'is_featured' => false,
                'is_active' => true,
                'visibility_status' => 'published',
                'tags' => ['sports', 'football', 'fitness'],
            ],
        ];

        foreach ($products as $data) {
            $data['slug'] = Str::slug($data['name']);
            Product::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );
        }

        $this->command->info('Successfully seeded 10 Daraz Bangladesh products!');
    }
}

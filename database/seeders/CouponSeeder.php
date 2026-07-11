<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'BD10',
                'type' => 'percent',
                'value' => 10,
                'minimum_order_amount' => 500,
                'usage_limit' => 500,
                'is_active' => true,
            ],
            [
                'code' => 'DHAKA100',
                'type' => 'fixed',
                'value' => 100,
                'minimum_order_amount' => 1000,
                'usage_limit' => 300,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}

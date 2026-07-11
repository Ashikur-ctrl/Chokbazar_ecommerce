<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Settings
    |--------------------------------------------------------------------------
    |
    | Configurable e-commerce settings for the marketplace.
    |
    */

    // Tax rate as a decimal (e.g., 0.08 = 8%)
    'tax_rate' => env('SHOP_TAX_RATE', 0.08),

    // Free shipping threshold
    'free_shipping_threshold' => env('SHOP_FREE_SHIPPING_THRESHOLD', 100),

    // Flat shipping rate
    'shipping_rate' => env('SHOP_SHIPPING_RATE', 10.00),

    // Default low stock threshold
    'low_stock_threshold' => env('SHOP_LOW_STOCK_THRESHOLD', 5),

    // Currency
    'currency' => env('SHOP_CURRENCY', 'BDT'),

    // Pagination
    'products_per_page' => env('SHOP_PRODUCTS_PER_PAGE', 12),

    // Default commission percentage for sellers
    'default_commission' => env('SHOP_DEFAULT_COMMISSION', 10),

    // Refund & return policy (shown at checkout)
    'return_policy' => env('SHOP_RETURN_POLICY', 'Products can be returned within 7 days of delivery in unused condition. Refunds are processed within 5-7 business days after the returned item is received and inspected.'),

    // Same-day delivery threshold (BDT) — orders above this and within Dhaka get same-day
    'same_day_threshold' => env('SHOP_SAME_DAY_THRESHOLD', 2000),

    // Delivery zones for hyperlocal
    'same_day_districts' => ['Dhaka'],
];

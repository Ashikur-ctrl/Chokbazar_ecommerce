<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addIndexIfNotExists(string $table, string|array $columns): void
    {
        $columns = (array) $columns;
        $indexName = $table . '_' . implode('_', (array) $columns) . '_index';

        try {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Index already exists — ignore
        }
    }

    public function up(): void
    {
        // Products table indexes
        $this->addIndexIfNotExists('products', 'category_id');
        // seller_id index already exists from add_seller_id_to_products_table migration
        $this->addIndexIfNotExists('products', 'is_active');
        $this->addIndexIfNotExists('products', 'is_featured');
        $this->addIndexIfNotExists('products', 'stock');

        // Orders table indexes
        $this->addIndexIfNotExists('orders', 'user_id');
        $this->addIndexIfNotExists('orders', 'status');
        $this->addIndexIfNotExists('orders', 'payment_status');
        $this->addIndexIfNotExists('orders', 'created_at');

        // Order items table indexes
        $this->addIndexIfNotExists('order_items', 'order_id');
        $this->addIndexIfNotExists('order_items', 'product_id');

        // Cart items table indexes
        $this->addIndexIfNotExists('cart_items', 'cart_id');
        $this->addIndexIfNotExists('cart_items', 'product_id');

        // Reviews table indexes
        $this->addIndexIfNotExists('reviews', 'product_id');
        $this->addIndexIfNotExists('reviews', 'user_id');
        $this->addIndexIfNotExists('reviews', 'is_approved');
    }

    public function down(): void
    {
        $indexes = [
            'products' => ['category_id', 'is_active', 'is_featured', 'stock'],
            'orders' => ['user_id', 'status', 'payment_status', 'created_at'],
            'order_items' => ['order_id', 'product_id'],
            'cart_items' => ['cart_id', 'product_id'],
            'reviews' => ['product_id', 'user_id', 'is_approved'],
        ];

        foreach ($indexes as $table => $columns) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $indexName = $table->getTable() . '_' . $column . '_index';
                    try {
                        $table->dropIndex($indexName);
                    } catch (\Exception $e) {
                        // Index may not exist — ignore
                    }
                }
            });
        }
    }
};

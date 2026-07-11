<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('sale_price');
            }
            if (! Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('sku');
            }
            if (! Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock');
            }
            if (! Schema::hasColumn('products', 'visibility_status')) {
                $table->string('visibility_status')->default('active')->after('is_active');
            }
            if (! Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('image');
            }
            if (! Schema::hasColumn('products', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique()->after('order_number');
            }
            if (! Schema::hasColumn('orders', 'packed_at')) {
                $table->timestamp('packed_at')->nullable()->after('shipped_at');
            }
            if (! Schema::hasColumn('orders', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('delivered_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['cost_price', 'tags', 'low_stock_threshold', 'visibility_status', 'seo_title', 'seo_description'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            foreach (['tracking_number', 'courier_name', 'invoice_number', 'packed_at', 'returned_at'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

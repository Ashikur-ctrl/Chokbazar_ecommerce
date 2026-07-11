<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('company_name');
            $table->string('logo')->nullable()->after('slug');
            $table->string('cover_image')->nullable()->after('logo');
            $table->json('business_documents')->nullable();
            $table->json('bank_details')->nullable();
            $table->string('national_id')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('trade_license')->nullable();
            $table->string('verification_status')->default('pending')->after('is_active');
            $table->string('return_policy')->nullable();
            $table->integer('shipping_days_min')->default(3);
            $table->integer('shipping_days_max')->default(7);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->string('business_type')->nullable();
            $table->string('year_established')->nullable();
            $table->string('website_url')->nullable();
            $table->string('whatsapp_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $columns = [
                'slug', 'logo', 'cover_image', 'business_documents', 'bank_details',
                'national_id', 'tin_number', 'trade_license', 'verification_status',
                'return_policy', 'shipping_days_min', 'shipping_days_max',
                'rating', 'rating_count', 'total_revenue', 'total_orders',
                'approved_at', 'minimum_order_amount', 'business_type',
                'year_established', 'website_url', 'whatsapp_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('sellers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

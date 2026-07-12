<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sourcing_type', 20)->default('local')->after('stock');
            $table->decimal('fob_price_usd', 10, 2)->nullable()->after('cost_price');
            $table->string('origin_country', 100)->nullable()->after('fob_price_usd');
            $table->integer('lead_time_days')->nullable()->after('moq');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sourcing_type', 'fob_price_usd', 'origin_country', 'lead_time_days']);
        });
    }
};

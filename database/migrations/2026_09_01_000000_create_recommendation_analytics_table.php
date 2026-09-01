<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('type'); // personalized, product_based, popular, frequently_bought_together, similar
            $table->string('position'); // e.g. home_page, product_page, cart_page
            $table->string('source_product_id')->nullable(); // for product-based recs
            $table->string('event'); // impression, click
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'type', 'event']);
            $table->index(['session_id', 'type', 'event']);
            $table->index(['product_id', 'event']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_analytics');
    }
};

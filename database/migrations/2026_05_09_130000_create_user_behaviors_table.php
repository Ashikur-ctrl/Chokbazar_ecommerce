<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For anonymous users
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['view', 'add_to_cart', 'purchase', 'wishlist', 'remove_from_cart']);
            $table->integer('weight')->default(1); // Importance weight for the action
            $table->json('metadata')->nullable(); // Additional data like time spent, source, etc.
            $table->timestamp('created_at');

            $table->index(['user_id', 'action']);
            $table->index(['session_id', 'action']);
            $table->index(['product_id', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};

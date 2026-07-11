<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fulfillment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->string('fulfillment_request_number')->unique();
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('seller_id');
            $table->index('status');
            $table->index('fulfillment_request_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fulfillment_requests');
    }
};

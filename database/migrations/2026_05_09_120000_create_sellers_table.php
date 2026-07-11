<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->text('description')->nullable();
            $table->string('api_key')->unique()->nullable();
            $table->enum('fulfillment_method', ['api', 'email', 'csv'])->default('email');
            $table->boolean('is_active')->default(true);
            $table->decimal('commission_percentage', 5, 2)->default(10);
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('api_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};

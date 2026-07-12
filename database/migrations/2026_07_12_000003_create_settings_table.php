<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'shipping_rate', 'value' => '60'],
            ['key' => 'tax_rate', 'value' => '0'],
            ['key' => 'currency_symbol', 'value' => '৳'],
            ['key' => 'store_name', 'value' => 'Chokbazar'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

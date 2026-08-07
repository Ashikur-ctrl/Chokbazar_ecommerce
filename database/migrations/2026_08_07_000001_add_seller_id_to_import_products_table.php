<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_products', function (Blueprint $table) {
            $table->foreignId('seller_id')
                ->nullable()
                ->after('import_batch_id')
                ->constrained('sellers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('import_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_id');
        });
    }
};

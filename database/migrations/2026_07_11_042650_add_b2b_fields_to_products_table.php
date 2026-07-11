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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'moq')) {
                $table->integer('moq')->default(1)->after('stock');
            }
            if (!Schema::hasColumn('products', 'is_wholesale')) {
                $table->boolean('is_wholesale')->default(false)->after('moq');
            }
            if (!Schema::hasColumn('products', 'is_b2b_only')) {
                $table->boolean('is_b2b_only')->default(false)->after('is_wholesale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['moq', 'is_wholesale', 'is_b2b_only'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

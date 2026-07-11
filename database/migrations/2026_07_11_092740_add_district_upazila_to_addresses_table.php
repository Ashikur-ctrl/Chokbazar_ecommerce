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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('district', 80)->nullable()->after('city');
            $table->string('upazila', 80)->nullable()->after('district');
            $table->string('post_code', 20)->nullable()->after('upazila');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['district', 'upazila', 'post_code']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('1688'); // room for other sources later
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->unsignedInteger('total_products')->default(0);
            $table->unsignedInteger('processed_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('import_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();

            $table->string('source_offer_id')->index(); // 1688 offerId
            $table->json('raw_payload')->nullable();     // original scraped response, kept for debugging

            $table->text('title_cn')->nullable();
            $table->text('title_en')->nullable();
            $table->longText('description_cn')->nullable();
            $table->longText('description_en')->nullable();

            $table->decimal('price_cny', 12, 2)->nullable();
            $table->decimal('price_bdt', 12, 2)->nullable();
            $table->decimal('fx_rate_used', 12, 6)->nullable(); // audit trail for the conversion

            $table->json('sku_data')->nullable();   // variants, MOQ, pricing tiers
            $table->json('images')->nullable();     // [{source_url, local_path, width, height}]

            // pending -> fetched -> translated -> images_done -> ready_for_review -> approved/rejected
            $table->string('status')->default('pending')->index();
            $table->text('error_message')->nullable(); // last failure reason, if any

            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(['import_batch_id', 'source_offer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_products');
        Schema::dropIfExists('import_batches');
    }
};

<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeFolderImportProductJob;
use App\Models\ImportBatch;
use App\Models\ImportProduct;
use App\Services\Imports\FolderProductReader;
use Illuminate\Console\Command;

class ImportFolderProducts extends Command
{
    protected $signature = 'import:folder
        {path : Folder containing product folders or product images}
        {--single : Treat the given folder as one product instead of scanning child folders}
        {--seller-id= : Attach the imported products to this seller ID}';

    protected $description = 'Create product import records from local folders and queue AI image analysis';

    public function handle(FolderProductReader $reader): int
    {
        $batch = ImportBatch::create([
            'source' => 'folder',
            'total_products' => 0,
            'created_by' => $this->getCurrentUserId(),
        ]);

        $products = $reader->read(
            path: $this->argument('path'),
            batchId: $batch->id,
            eachFolder: !$this->option('single'),
        );

        if (empty($products)) {
            $batch->delete();
            $this->error('No product images found. Use jpg, jpeg, png, or webp files.');
            return Command::FAILURE;
        }

        $batch->update(['total_products' => count($products)]);

        $this->info("Created folder import batch #{$batch->id} with " . count($products) . ' products.');

        $queued = 0;

        foreach ($products as $product) {
            try {
                $item = ImportProduct::create([
                    'import_batch_id' => $batch->id,
                    'seller_id' => $this->option('seller-id') ? (int) $this->option('seller-id') : null,
                    'source_offer_id' => $product['source_id'],
                    'raw_payload' => [
                        'source' => 'folder',
                        'source_path' => $product['source_path'],
                        'notes' => $product['notes'],
                        'metadata' => $product['metadata'],
                    ],
                    'images' => $product['images'],
                    'status' => 'pending',
                ]);

                AnalyzeFolderImportProductJob::dispatch($item->id);
                $this->line("  Queued: {$product['source_id']}");
                $queued++;
            } catch (\Throwable $e) {
                $this->warn("  Skipped {$product['source_id']}: {$e->getMessage()}");
            }
        }

        if ($queued === 0) {
            $batch->delete();
            $this->error('No products could be queued. Check the folder contents and try again.');
            return Command::FAILURE;
        }

        $this->info("Queued {$queued} products. Monitor progress in Filament -> Imports -> Product Import Review Queue.");

        return Command::SUCCESS;
    }

    protected function getCurrentUserId(): ?int
    {
        return auth()->id() ?: null;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ImportBatch;
use App\Models\ImportProduct;
use App\Jobs\FetchProductFromSourceJob;
use Illuminate\Console\Command;

class Import1688Batch extends Command
{
    protected $signature = 'import:1688 {offerIds : Comma-separated 1688 offer IDs}';
    protected $description = 'Start a new 1688 product import batch from offer IDs';

    public function handle(): int
    {
        $offerIds = array_map('trim', explode(',', $this->argument('offerIds')));

        if (empty($offerIds) || $offerIds === ['']) {
            $this->error('Provide at least one offer ID.');
            return Command::FAILURE;
        }

        $batch = ImportBatch::create([
            'source' => '1688',
            'total_products' => count($offerIds),
            'created_by' => $this->getCurrentUserId(),
        ]);

        $this->info("Created batch #{$batch->id} with {$batch->total_products} products.");

        foreach ($offerIds as $offerId) {
            $item = ImportProduct::create([
                'import_batch_id' => $batch->id,
                'source_offer_id' => $offerId,
                'status' => 'pending',
            ]);

            FetchProductFromSourceJob::dispatch($item->id);
            $this->line("  Queued: {$offerId}");
        }

        $this->info('Done. Monitor progress in Filament → Imports → 1688 Review Queue.');

        return Command::SUCCESS;
    }

    protected function getCurrentUserId(): ?int
    {
        return auth()->id() ?: null;
    }
}

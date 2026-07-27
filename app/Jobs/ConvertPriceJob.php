<?php

namespace App\Jobs;

use App\Models\ImportProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ConvertPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $importProductId,
    ) {
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        $item = ImportProduct::find($this->importProductId);
        if (!$item || $item->status !== 'images_done') {
            return;
        }

        try {
            // Rate is refreshed once a day by RefreshFxRateJob (see scheduler below).
            // Never call an FX API per-product — that's wasted quota and a needless
            // point of failure on every single import.
            $rate = Cache::get('fx_rate_cny_bdt');

            if (!$rate) {
                throw new \RuntimeException('No cached CNY→BDT rate available yet');
            }

            $markupPercent = config('services.import_1688.markup_percent', 25);

            $basePriceBdt = $item->price_cny * $rate;
            $finalPriceBdt = round($basePriceBdt * (1 + $markupPercent / 100), 2);

            $item->update([
                'price_bdt' => $finalPriceBdt,
                'fx_rate_used' => $rate,
                'status' => 'ready_for_review',
            ]);

        } catch (\Throwable $e) {
            Log::warning("Price conversion failed for import product {$item->id}: {$e->getMessage()}");
            $item->markFailed($e->getMessage());
            $this->fail($e);
        }
    }
}

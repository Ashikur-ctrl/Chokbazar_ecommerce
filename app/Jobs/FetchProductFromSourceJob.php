<?php

namespace App\Jobs;

use App\Models\ImportProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchProductFromSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30; // seconds, doubles via retryUntil below if you want exponential

    public function __construct(
        public int $importProductId,
    ) {
        $this->onQueue('imports');
    }

    // Prevents this job type from exceeding your third-party API plan's rate limit.
    // Tune the "30" to whatever your chosen provider (Parse.bot / TMAPI / Apify) allows.
    public function middleware(): array
    {
        return [(new RateLimited('source-1688-fetch'))];
    }

    public function handle(): void
    {
        $item = ImportProduct::find($this->importProductId);
        if (!$item) {
            return; // deleted before job ran, nothing to do
        }

        $apiKey = config('services.import_1688.api_key');
        $baseUrl = config('services.import_1688.base_url');

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->retry(2, 500)
                ->post("{$baseUrl}/get_product_details", [
                    'offer_id' => $item->source_offer_id,
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException("Source API returned {$response->status()}");
            }

            $payload = $response->json();
            $data = $payload['data'] ?? $payload;

            $item->update([
                'raw_payload' => $payload,
                'title_cn' => $data['title'] ?? null,
                'description_cn' => $data['description'] ?? null,
                'price_cny' => $data['min_price'] ?? null,
                'sku_data' => $data['skuModel'] ?? null,
                'status' => 'fetched',
            ]);

            // chain the next step yourself if you're not using Bus::chain() upstream
            TranslateProductJob::dispatch($item->id);

        } catch (\Throwable $e) {
            Log::warning("1688 fetch failed for offer {$item->source_offer_id}: {$e->getMessage()}");
            $item->markFailed($e->getMessage());
            $this->fail($e);
        }
    }
}

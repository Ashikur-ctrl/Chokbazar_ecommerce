<?php

namespace App\Jobs;

use App\Models\ImportProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateProductJob implements ShouldQueue
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
        if (!$item || $item->status !== 'fetched') {
            return;
        }

        try {
            $prompt = <<<PROMPT
                Translate the following product title and description from Chinese to natural,
                sales-ready English suitable for a Bangladesh e-commerce marketplace.
                Return ONLY valid JSON, no markdown fences, in this exact shape:
                {"title_en": "...", "description_en": "..."}

                Title: {$item->title_cn}
                Description: {$item->description_cn}
                PROMPT;

            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.api_key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-sonnet-4-6',
                    'max_tokens' => 1000,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException("Translation API returned {$response->status()}");
            }

            $text = $response->json('content.0.text', '');
            $clean = trim(str_replace(['```json', '```'], '', $text));
            $parsed = json_decode($clean, true);

            if (!$parsed || !isset($parsed['title_en'])) {
                throw new \RuntimeException('Could not parse translation response');
            }

            $item->update([
                'title_en' => $parsed['title_en'],
                'description_en' => $parsed['description_en'] ?? null,
                'status' => 'translated',
            ]);

            DownloadProductImagesJob::dispatch($item->id);

        } catch (\Throwable $e) {
            Log::warning("Translation failed for import product {$item->id}: {$e->getMessage()}");
            $item->markFailed($e->getMessage());
            $this->fail($e);
        }
    }
}

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
            $apiKey = config('services.gemini.api_key');

            if ($apiKey) {
                $prompt = <<<PROMPT
                    Translate the following product title and description from Chinese to natural,
                    sales-ready English suitable for a Bangladesh e-commerce marketplace.
                    Return ONLY valid JSON, no markdown fences, in this exact shape:
                    {"title_en": "...", "description_en": "..."}

                    Title: {$item->title_cn}
                    Description: {$item->description_cn}
                    PROMPT;

                $response = Http::withHeaders([
                    'content-type' => 'application/json',
                ])
                    ->timeout(30)
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent?key=' . $apiKey, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text', '');
                    $clean = trim(str_replace(['```json', '```'], '', $text));
                    $parsed = json_decode($clean, true);

                    if ($parsed && isset($parsed['title_en'])) {
                        $item->update([
                            'title_en' => $parsed['title_en'],
                            'description_en' => $parsed['description_en'] ?? null,
                            'status' => 'translated',
                        ]);

                        DownloadProductImagesJob::dispatch($item->id);
                        return;
                    }
                }

                Log::warning("Translation API failed ({$response->status()}), falling back to raw Chinese");
            } else {
                Log::info('No AI translation key configured, falling back to raw Chinese');
            }

            // Fallback: pass through raw Chinese fields so the pipeline keeps moving
            $item->update([
                'title_en' => $item->title_cn,
                'description_en' => $item->description_cn,
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

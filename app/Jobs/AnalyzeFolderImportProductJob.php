<?php

namespace App\Jobs;

use App\Models\ImportProduct;
use App\Services\Gemini\InteractionClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnalyzeFolderImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 90;

    public function __construct(
        public int $importProductId,
    ) {
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        $item = ImportProduct::find($this->importProductId);

        if (!$item || $item->status !== 'pending' || !in_array($item->batch?->source, ['folder', 'seller_ai'], true)) {
            return;
        }

        try {
            $payload = $item->raw_payload ?? [];
            $metadata = $payload['metadata'] ?? [];
            $notes = $payload['notes'] ?? null;

            $analysis = $this->analyzeWithGemini($item, $metadata, $notes);

            $item->update([
                'title_en' => $analysis['title'] ?? $metadata['title'] ?? Str::headline($item->source_offer_id),
                'description_en' => $analysis['description'] ?? $notes ?? $metadata['description'] ?? null,
                'price_bdt' => $this->resolvePrice($analysis, $metadata),
                'sku_data' => [
                    'detected_attributes' => $analysis['attributes'] ?? [],
                    'tags' => $analysis['tags'] ?? [],
                    'market_notes' => $analysis['market_notes'] ?? null,
                    'price_range_bdt' => $analysis['price_range_bdt'] ?? null,
                    'market_sources' => $analysis['market_sources'] ?? [],
                    'source_metadata' => $metadata,
                ],
                'status' => 'ready_for_review',
            ]);

            $item->batch?->incrementProcessed();
        } catch (\Throwable $e) {
            Log::warning("Folder product analysis failed for import product {$item->id}: {$e->getMessage()}");
            $item->markFailed($e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function analyzeWithGemini(ImportProduct $item, array $metadata, ?string $notes): array
    {
        $client = app(InteractionClient::class);

        if (!$client->hasApiKey()) {
            return $this->fallbackAnalysis($item, $metadata, $notes);
        }

        $input = [[
            'type' => 'text',
            'text' => $this->prompt($metadata, $notes),
        ]];

        foreach (array_slice($item->images ?? [], 0, 5) as $image) {
            $localPath = is_array($image) ? ($image['local_path'] ?? null) : null;

            if (!$localPath || !Storage::disk('public')->exists($localPath)) {
                continue;
            }

            $body = Storage::disk('public')->get($localPath);
            $input[] = [
                'type' => 'image',
                'mime_type' => $this->mimeType($localPath),
                'data' => base64_encode($body),
            ];
        }

        $tools = config('services.gemini.search_grounding', false)
            ? [['google_search' => []]]
            : [];

        $interaction = $client->create($input, $tools, 60);

        if (!$interaction) {
            return $this->fallbackAnalysis($item, $metadata, $notes);
        }

        $text = $interaction['text'] ?? '';
        $clean = trim(str_replace(['```json', '```'], '', $text));
        $parsed = json_decode($clean, true);

        if (!is_array($parsed)) {
            return $this->fallbackAnalysis($item, $metadata, $notes);
        }

        $parsed['market_sources'] ??= $interaction['sources'] ?? [];

        return $parsed;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function prompt(array $metadata, ?string $notes): string
    {
        $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
            You are preparing a product listing for Chokbazar, a Bangladesh e-commerce marketplace.
            Use the product images, optional seller notes, and metadata to infer practical product requirements.
            Be conservative: do not invent exact specs, brand names, certifications, or prices unless visible or supplied.
            If only one sample image is provided, still create the best possible draft but mark uncertain details as null.
            When web search is available, search for live Bangladesh market listings for this product type (e.g. Daraz, local
            shops) and base the suggested price and price range on those real listings. If search results are unavailable,
            estimate a competitive Bangladesh retail price in BDT only when the product type is clear; otherwise return null.
            Return ONLY valid JSON, no markdown fences, in this exact shape:
            {
              "title": "short sales-ready English title",
              "description": "clean HTML product description with bullet points",
              "suggested_price_bdt": 0,
              "price_range_bdt": {"min": null, "max": null},
              "attributes": {"material": null, "color": null, "size": null, "use_case": null},
              "tags": ["tag"],
              "market_notes": "short note explaining the pricing/content assumptions and what live listings showed",
              "market_sources": ["https://example.com/listing"]
            }

            Seller notes:
            {$notes}

            Metadata JSON:
            {$metadataJson}
            PROMPT;
    }

    /**
     * Pick the final BDT price: prefer a positive AI suggestion, then folder
     * metadata, then null. A zero from the model is treated as "unknown" so we
     * never publish a free product by accident.
     *
     * @param array<string, mixed> $analysis
     * @param array<string, mixed> $metadata
     */
    private function resolvePrice(array $analysis, array $metadata): ?float
    {
        $candidates = [
            $analysis['suggested_price_bdt'] ?? null,
            $metadata['price_bdt'] ?? null,
            $metadata['price'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_numeric($candidate) && (float) $candidate > 0) {
                return (float) $candidate;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function fallbackAnalysis(ImportProduct $item, array $metadata, ?string $notes): array
    {
        return [
            'title' => $metadata['title'] ?? Str::headline($item->source_offer_id),
            'description' => $metadata['description'] ?? $notes ?? Str::headline($item->source_offer_id),
            'suggested_price_bdt' => $metadata['price_bdt'] ?? $metadata['price'] ?? null,
            'price_range_bdt' => null,
            'attributes' => [],
            'tags' => $metadata['tags'] ?? [],
            'market_notes' => 'AI key was not configured or analysis failed, so the listing uses folder notes/metadata only.',
            'market_sources' => [],
        ];
    }

    private function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}

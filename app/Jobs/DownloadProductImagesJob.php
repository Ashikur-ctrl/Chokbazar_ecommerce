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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Laravel\Facades\Image;

class DownloadProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60; // keep well under your host's max_execution_time

    // Hard cap — protects disk quota and execution time on shared hosting.
    // Raise later only if you've confirmed you have headroom.
    private const MAX_IMAGES_PER_PRODUCT = 5;

    public function __construct(
        public int $importProductId,
    ) {
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        $item = ImportProduct::find($this->importProductId);
        if (!$item || $item->status !== 'translated') {
            return;
        }

        $payload = $item->raw_payload;
        $data = $payload['data'] ?? $payload;
        $sourceImages = $data['images'] ?? $item->raw_payload['images'] ?? $item->raw_payload['main_images'] ?? [];
        $sourceImages = array_slice($sourceImages, 0, self::MAX_IMAGES_PER_PRODUCT);

        $stored = [];

        foreach ($sourceImages as $index => $img) {
            $url = is_array($img) ? ($img['fullPathImageURI'] ?? $img['url'] ?? null) : $img;
            if (!$url) {
                continue;
            }

            try {
                $response = Http::timeout(15)->get($url);
                if (!$response->successful()) {
                    continue; // skip this one image, don't fail the whole product
                }

                $filename = "imports/{$item->import_batch_id}/{$item->source_offer_id}_{$index}.jpg";

                // resize down to keep disk usage sane on shared hosting
                $image = Image::decode($response->body())
                    ->scaleDown(width: 1200)
                    ->encode(new JpegEncoder(quality: 82));

                Storage::disk('public')->put($filename, (string) $image);

                $stored[] = [
                    'source_url' => $url,
                    'local_path' => $filename,
                ];

            } catch (\Throwable $e) {
                Log::warning("Image download failed for {$url}: {$e->getMessage()}");
                // continue to next image rather than aborting the product
            }
        }

        if (empty($stored)) {
            $item->markFailed('No images could be downloaded');
            return;
        }

        $item->update([
            'images' => $stored,
            'status' => 'images_done',
        ]);

        ConvertPriceJob::dispatch($item->id);
    }
}

<?php

namespace App\Services\Imports;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FolderProductReader
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * @return array<int, array{
     *     source_id: string,
     *     source_path: string,
     *     notes: ?string,
     *     metadata: array<string, mixed>,
     *     images: array<int, array<string, string>>
     * }>
     */
    public function read(string $path, int $batchId, bool $eachFolder = true): array
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR);

        if (!File::isDirectory($path)) {
            throw new \InvalidArgumentException("Folder does not exist: {$path}");
        }

        $folders = $eachFolder ? $this->productFolders($path) : [$path];
        $usedSourceIds = [];

        return collect($folders)
            ->map(function (string $folder) use ($batchId, &$usedSourceIds) {
                $product = $this->readProductFolder($folder, $batchId);
                $product['source_id'] = $this->uniqueSourceId($product['source_id'], $usedSourceIds);

                return $product;
            })
            ->filter(fn (array $product) => !empty($product['images']))
            ->values()
            ->all();
    }

    /**
     * Make sure a source id is unique within the batch so the
     * (import_batch_id, source_offer_id) unique constraint can't fire.
     *
     * @param array<string, int> $usedSourceIds
     */
    private function uniqueSourceId(string $sourceId, array &$usedSourceIds): string
    {
        $count = $usedSourceIds[$sourceId] ?? 0;
        $count++;
        $usedSourceIds[$sourceId] = $count;

        return $count === 1 ? $sourceId : "{$sourceId}-{$count}";
    }

    /**
     * @return array<int, string>
     */
    private function productFolders(string $path): array
    {
        $directories = collect(File::directories($path))->values();

        if ($directories->isEmpty()) {
            return [$path];
        }

        return $directories->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function readProductFolder(string $folder, int $batchId): array
    {
        $sourceId = Str::slug(basename($folder)) ?: 'folder-product-' . Str::random(8);
        $metadata = $this->metadataFromFolder($folder);
        $notes = $this->notesFromFolder($folder);
        $images = $this->storeImages($folder, $batchId, $sourceId);

        return [
            'source_id' => $sourceId,
            'source_path' => $folder,
            'notes' => $notes,
            'metadata' => $metadata,
            'images' => $images,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function metadataFromFolder(string $folder): array
    {
        $jsonPath = $folder . DIRECTORY_SEPARATOR . 'product.json';

        if (!File::exists($jsonPath)) {
            return [];
        }

        $decoded = json_decode(File::get($jsonPath), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function notesFromFolder(string $folder): ?string
    {
        foreach (['info.txt', 'notes.txt', 'description.txt'] as $filename) {
            $path = $folder . DIRECTORY_SEPARATOR . $filename;

            if (File::exists($path)) {
                return trim(File::get($path)) ?: null;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function storeImages(string $folder, int $batchId, string $sourceId): array
    {
        return collect(File::files($folder))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::IMAGE_EXTENSIONS, true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->take(8)
            ->values()
            ->map(function ($file, int $index) use ($batchId, $sourceId) {
                $extension = strtolower($file->getExtension()) === 'jpeg' ? 'jpg' : strtolower($file->getExtension());
                $localPath = "imports/{$batchId}/folder/{$sourceId}_{$index}.{$extension}";

                Storage::disk('public')->put($localPath, File::get($file->getPathname()));

                return [
                    'source_path' => $file->getPathname(),
                    'local_path' => $localPath,
                ];
            })
            ->all();
    }
}

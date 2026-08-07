<?php

namespace App\Services\Gemini;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InteractionClient
{
    public function hasApiKey(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    /**
     * @param array<int, array<string, mixed>>|string $input
     * @param array<int, array<string, mixed>> $tools
     */
    public function create(array|string $input, array $tools = [], int $timeout = 60): ?array
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return null;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->timeout($timeout)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent?key=' . $apiKey, [
                'contents' => [[
                    'parts' => $this->buildParts($input),
                ]],
                'tools' => $tools ?: null,
            ]);

        if (!$response->successful()) {
            Log::warning("Gemini API failed ({$response->status()})");

            return null;
        }

        return [
            'text' => $this->outputText($response),
            'sources' => $this->sourceUrls($response),
            'response' => $response->json(),
        ];
    }

    /**
     * Convert the app's input shape (text + image parts) into the
     * generateContent parts format: text becomes {"text": "..."} and image
     * becomes {"inline_data": {"mime_type": "...", "data": "..."}}.
     *
     * @param array<int, array<string, mixed>>|string $input
     * @return array<int, array<string, mixed>>
     */
    private function buildParts(array|string $input): array
    {
        if (is_string($input)) {
            return [['text' => $input]];
        }

        return collect($input)->map(function (array $part): array {
            if (($part['type'] ?? null) === 'image') {
                return [
                    'inline_data' => [
                        'mime_type' => $part['mime_type'] ?? 'image/jpeg',
                        'data' => $part['data'] ?? '',
                    ],
                ];
            }

            return ['text' => $part['text'] ?? ''];
        })->values()->all();
    }

    private function outputText(Response $response): string
    {
        return collect($response->json('candidates.0.content.parts', []))
            ->filter(fn (array $part) => filled($part['text'] ?? null))
            ->pluck('text')
            ->implode("\n");
    }

    /**
     * @return array<int, string>
     */
    private function sourceUrls(Response $response): array
    {
        return collect($response->json('candidates.0.groundingMetadata.groundingChunks', []))
            ->pluck('web.uri')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

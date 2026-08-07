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

        $payload = [
            'model' => config('services.gemini.model'),
            'input' => $input,
            'store' => false,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey,
        ])
            ->timeout($timeout)
            ->post('https://generativelanguage.googleapis.com/v1beta2/interactions', $payload);

        if (!$response->successful()) {
            Log::warning("Gemini Interactions API failed ({$response->status()})");

            return null;
        }

        return [
            'text' => $this->outputText($response),
            'sources' => $this->sourceUrls($response),
            'response' => $response->json(),
        ];
    }

    private function outputText(Response $response): string
    {
        $steps = collect($response->json('steps', []));
        $modelOutput = $steps
            ->filter(fn (array $step) => ($step['type'] ?? null) === 'model_output')
            ->last();

        if (!$modelOutput) {
            return '';
        }

        return collect($modelOutput['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->filter()
            ->implode("\n");
    }

    /**
     * @return array<int, string>
     */
    private function sourceUrls(Response $response): array
    {
        $steps = collect($response->json('steps', []));

        return $steps
            ->flatMap(function (array $step) {
                return collect($step['content'] ?? [])
                    ->flatMap(fn (array $content) => collect($content['annotations'] ?? [])
                        ->pluck('url'));
            })
            ->merge($steps->pluck('url'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

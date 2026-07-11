<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedxService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.redx.api_key', '');
        $this->baseUrl = config('services.redx.base_url', 'https://openapi.redx.com.bd/v1');
    }

    protected function headers(): array
    {
        return [
            'Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . '/merchant/order', [
                    'sender_name' => config('app.name'),
                    'sender_phone' => $data['sender_phone'] ?? '',
                    'recipient_name' => $data['customer_name'],
                    'recipient_phone' => $data['customer_phone'],
                    'recipient_address' => $data['shipping_address'],
                    'delivery_area' => $data['area'] ?? '',
                    'product_price' => $data['cod_amount'] ?? 0,
                    'customer_reference' => $data['order_number'] ?? '',
                    'note' => $data['note'] ?? '',
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (($result['status'] ?? 'success') === 'success') {
                    return [
                        'success' => true,
                        'tracking_id' => $result['tracking_id'] ?? $result['data']['tracking_id'] ?? '',
                    ];
                }
            }

            Log::error('Redx create order failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to create RedX order.'];

        } catch (\Exception $e) {
            Log::error('Redx create exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'RedX service error.'];
        }
    }

    public function trackOrder(string $trackingId): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl . '/merchant/order/' . $trackingId . '/status');

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'status' => $result['data']['status'] ?? $result['status'] ?? 'unknown',
                    'data' => $result,
                ];
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('Redx track exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }
}

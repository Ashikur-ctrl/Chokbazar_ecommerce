<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.steadfast.api_key', '');
        $this->secretKey = config('services.steadfast.secret_key', '');
        $this->baseUrl = config('services.steadfast.base_url', 'https://portal.steadfast.com.bd/api/v1');
    }

    protected function headers(): array
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . '/create_order', [
                    'recipient_name' => $data['customer_name'],
                    'recipient_phone' => $data['customer_phone'],
                    'recipient_address' => $data['shipping_address'],
                    'cod_amount' => $data['cod_amount'] ?? 0,
                    'note' => $data['note'] ?? '',
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (($result['status'] ?? 200) === 200) {
                    return [
                        'success' => true,
                        'tracking_id' => $result['consignment']['tracking_code'] ?? '',
                        'consignment_id' => $result['consignment']['id'] ?? null,
                    ];
                }
            }

            Log::error('Steadfast create order failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to create courier order.'];

        } catch (\Exception $e) {
            Log::error('Steadfast exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Courier service error.'];
        }
    }

    public function trackOrder(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl . '/status_by_trackingcode/' . $trackingCode);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'status' => $result['delivery_status'] ?? 'unknown',
                    'data' => $result,
                ];
            }

            return ['success' => false, 'message' => 'Tracking failed.'];

        } catch (\Exception $e) {
            Log::error('Steadfast track exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Tracking error.'];
        }
    }
}

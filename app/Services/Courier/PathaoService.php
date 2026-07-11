<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.pathao.base_url', 'https://api-hermes.pathao.com');
        $this->clientId = config('services.pathao.client_id', '');
        $this->clientSecret = config('services.pathao.client_secret', '');
        $this->username = config('services.pathao.username', '');
        $this->password = config('services.pathao.password', '');
    }

    public function grantToken(): ?string
    {
        try {
            $response = Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $this->username,
                'password' => $this->password,
                'grant_type' => 'password',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['access_token'] ?? null;

                if ($this->token) {
                    cache()->put('pathao_token', $this->token, 3500);
                    return $this->token;
                }
            }

            Log::error('Pathao token grant failed', ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Pathao token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function getToken(): ?string
    {
        return cache()->get('pathao_token') ?: $this->grantToken();
    }

    public function createOrder(array $data): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Pathao auth failed.'];
        }

        try {
            $response = Http::withToken($token)
                ->post($this->baseUrl . '/aladdin/api/v1/orders', [
                    'store_id' => $data['store_id'] ?? 0,
                    'merchant_order_id' => $data['order_number'] ?? '',
                    'recipient_name' => $data['customer_name'],
                    'recipient_phone' => $data['customer_phone'],
                    'recipient_address' => $data['shipping_address'],
                    'recipient_city' => $data['city'] ?? 1,
                    'recipient_zone' => $data['zone'] ?? 1,
                    'recipient_area' => $data['area'] ?? 1,
                    'amount_to_collect' => $data['cod_amount'] ?? 0,
                    'item_description' => $data['note'] ?? 'Order items',
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'tracking_id' => $result['data']['tracking_id'] ?? '',
                    'consignment_id' => $result['data']['consignment_id'] ?? null,
                ];
            }

            Log::error('Pathao create order failed', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Failed to create Pathao order.'];

        } catch (\Exception $e) {
            Log::error('Pathao create exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Pathao service error.'];
        }
    }

    public function trackOrder(string $trackingId): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false];
        }

        try {
            $response = Http::withToken($token)
                ->get($this->baseUrl . '/aladdin/api/v1/orders/' . $trackingId . '/track');

            if ($response->successful()) {
                $result = $response->json();
                return ['success' => true, 'status' => $result['data']['status'] ?? 'unknown', 'data' => $result['data'] ?? []];
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('Pathao track exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }
}

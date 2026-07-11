<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashService
{
    protected string $baseUrl;
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;
    protected ?string $token = null;

    public function __construct()
    {
        $sandbox = config('services.bkash.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
        $this->appKey = config('services.bkash.app_key', '');
        $this->appSecret = config('services.bkash.app_secret', '');
        $this->username = config('services.bkash.username', '');
        $this->password = config('services.bkash.password', '');
    }

    public function grantToken(): ?string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-app-key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['id_token'] ?? null;

                if ($this->token) {
                    // Cache for 1 hour (tokens expire in 1h)
                    cache()->put('bkash_token', $this->token, 3500);
                    return $this->token;
                }
            }

            Log::error('bKash token grant failed', ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('bKash token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function getToken(): ?string
    {
        return cache()->get('bkash_token') ?: $this->grantToken();
    }

    public function createPayment(Order $order): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'bKash service unavailable. Please try COD.'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-app-key' => $this->appKey,
                'Authorization' => $token,
            ])->post($this->baseUrl . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $order->customer_phone ?? $order->customer_email,
                'callbackURL' => route('payment.bkash.callback'),
                'amount' => (string) $order->total_amount,
                'currency' => 'BDT',
                'merchantInvoiceNumber' => $order->order_number,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (($data['statusCode'] ?? '') === '0000' && !empty($data['bkashURL'])) {
                    return [
                        'success' => true,
                        'gateway_url' => $data['bkashURL'],
                        'payment_id' => $data['paymentID'] ?? null,
                    ];
                }
            }

            Log::error('bKash create payment failed', ['order' => $order->order_number, 'response' => $response->body()]);
            return ['success' => false, 'message' => 'bKash payment creation failed.'];

        } catch (\Exception $e) {
            Log::error('bKash create payment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'bKash service error.'];
        }
    }

    public function executePayment(string $paymentId): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'bKash service unavailable.'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-app-key' => $this->appKey,
                'Authorization' => $token,
            ])->post($this->baseUrl . '/tokenized/checkout/execute', [
                'paymentID' => $paymentId,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (($data['statusCode'] ?? '') === '0000' && ($data['transactionStatus'] ?? '') === 'Completed') {
                    return [
                        'success' => true,
                        'transaction_id' => $data['trxID'] ?? '',
                        'payment_id' => $paymentId,
                    ];
                }
            }

            return ['success' => false, 'message' => 'bKash payment execution failed.'];

        } catch (\Exception $e) {
            Log::error('bKash execute exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'bKash execution error.'];
        }
    }

    public function queryPayment(string $paymentId): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-app-key' => $this->appKey,
                'Authorization' => $token,
            ])->post($this->baseUrl . '/tokenized/checkout/status', [
                'paymentID' => $paymentId,
            ]);

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false];

        } catch (\Exception $e) {
            return ['success' => false];
        }
    }
}

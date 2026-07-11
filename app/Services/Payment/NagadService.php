<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NagadService
{
    protected string $baseUrl;
    protected string $merchantId;
    protected string $merchantNumber;
    protected string $publicKey;
    protected string $privateKey;

    public function __construct()
    {
        $sandbox = config('services.nagad.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://sandbox.mynagad.com/api-contract/api/v2'
            : 'https://api.mynagad.com/api-contract/api/v2';
        $this->merchantId = config('services.nagad.merchant_id', '');
        $this->merchantNumber = config('services.nagad.merchant_number', '');
        $this->publicKey = config('services.nagad.public_key', '');
        $this->privateKey = config('services.nagad.private_key', '');
    }

    protected function generateSensitiveData(array $data): string
    {
        $json = json_encode($data);
        $encrypted = null;
        openssl_public_encrypt($json, $encrypted, $this->publicKey);
        return base64_encode($encrypted);
    }

    protected function generateSignature(string $data): string
    {
        $signature = null;
        openssl_sign($data, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function createPayment(Order $order): array
    {
        $orderId = $order->order_number . '-' . time();
        $dateTime = now()->format('YmdHis');
        $sensitiveData = $this->generateSensitiveData([
            'merchantId' => $this->merchantId,
            'orderId' => $orderId,
            'currencyCode' => '050',
            'amount' => (string) $order->total_amount,
            'challenge' => $dateTime,
        ]);

        $data = [
            'merchantId' => $this->merchantId,
            'orderId' => $orderId,
            'currencyCode' => '050',
            'amount' => (string) $order->total_amount,
            'challenge' => $dateTime,
            'sensitiveData' => $sensitiveData,
            'signature' => $this->generateSignature($sensitiveData),
            'callbackURL' => route('payment.nagad.callback'),
            'merchantCallbackURL' => route('payment.nagad.callback'),
            'additionalInfo' => json_encode([
                'productDetails' => 'Order #' . $order->order_number,
            ]),
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-KM-IP-V2' => '1',
                'X-KM-Api-Version' => 'v-0.2.0',
            ])->post($this->baseUrl . '/checkout/initialize/' . $this->merchantId . '/' . $orderId, $data);

            if ($response->successful()) {
                $result = $response->json();

                if (!empty($result['callBackUrl'])) {
                    return [
                        'success' => true,
                        'gateway_url' => $result['callBackUrl'],
                        'order_id' => $orderId,
                    ];
                }
            }

            Log::error('Nagad create payment failed', ['order' => $order->order_number, 'response' => $response->body()]);
            return ['success' => false, 'message' => 'Nagad payment initialization failed.'];

        } catch (\Exception $e) {
            Log::error('Nagad create exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Nagad service error.'];
        }
    }

    public function verifyPayment(array $queryParams): array
    {
        $paymentRefId = $queryParams['payment_ref_id'] ?? '';

        if (empty($paymentRefId)) {
            return ['success' => false, 'message' => 'Invalid payment reference.'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-KM-IP-V2' => '1',
                'X-KM-Api-Version' => 'v-0.2.0',
            ])->get($this->baseUrl . '/checkout/complete/' . $paymentRefId);

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'Success') {
                    return [
                        'success' => true,
                        'transaction_id' => $data['issuerPaymentRefNo'] ?? $paymentRefId,
                        'amount' => $data['amount'] ?? 0,
                        'reference' => $paymentRefId,
                    ];
                }
            }

            return ['success' => false, 'message' => 'Payment verification failed.'];

        } catch (\Exception $e) {
            Log::error('Nagad verify exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Verification error.'];
        }
    }
}

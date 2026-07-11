<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $sandbox;

    public function __construct()
    {
        $this->storeId = config('services.sslcommerz.store_id', '');
        $this->storePassword = config('services.sslcommerz.store_password', '');
        $this->sandbox = config('services.sslcommerz.sandbox', true);
    }

    protected function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://secure.sslcommerz.com';
    }

    public function initPayment(Order $order): array
    {
        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $order->total_amount,
            'currency' => $order->currency ?? 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'cus_name' => $order->customer_name,
            'cus_email' => $order->customer_email,
            'cus_phone' => $order->customer_phone,
            'cus_add1' => $order->shipping_address,
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'Courier',
            'product_name' => 'Order #' . $order->order_number,
            'product_category' => 'General',
            'product_profile' => 'general',
        ];

        try {
            $response = Http::asForm()->post($this->baseUrl() . '/gwprocess/v4/api.php', $postData);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SSLCommerz init response', ['order' => $order->order_number, 'status' => $data['status'] ?? 'unknown']);

                if (($data['status'] ?? '') === 'SUCCESS') {
                    return [
                        'success' => true,
                        'gateway_url' => $data['GatewayPageURL'],
                        'session_key' => $data['sessionkey'] ?? null,
                    ];
                }
            }

            Log::error('SSLCommerz init failed', ['order' => $order->order_number, 'response' => $response->body()]);
            return ['success' => false, 'message' => 'Payment gateway initialization failed.'];

        } catch (\Exception $e) {
            Log::error('SSLCommerz exception', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment gateway error. Please try again.'];
        }
    }

    public function validatePayment(array $requestData): array
    {
        $orderNumber = $requestData['tran_id'] ?? null;
        $amount = $requestData['amount'] ?? 0;
        $currency = $requestData['currency'] ?? 'BDT';

        try {
            $response = Http::asForm()->post($this->baseUrl() . '/validator/api/validationserverAPI.php', [
                'val_id' => $requestData['val_id'] ?? '',
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'VALID' || ($data['status'] ?? '') === 'VALIDATED') {
                    return [
                        'success' => true,
                        'order_number' => $orderNumber,
                        'transaction_id' => $requestData['bank_tran_id'] ?? '',
                        'amount' => $amount,
                        'currency' => $currency,
                    ];
                }
            }

            return ['success' => false, 'message' => 'Payment validation failed.'];

        } catch (\Exception $e) {
            Log::error('SSLCommerz validation exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment validation error.'];
        }
    }
}

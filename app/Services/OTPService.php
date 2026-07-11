<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OTPService
{
    /**
     * Generate and store OTP for an order.
     */
    public function generate(Order $order, int $length = 6): string
    {
        $otp = (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);

        cache()->put("order_otp_{$order->id}", $otp, 600); // 10 minutes

        Log::info("OTP generated for order {$order->order_number}: {$otp}");

        return $otp;
    }

    /**
     * Verify OTP for an order.
     */
    public function verify(Order $order, string $otp): bool
    {
        $cached = cache()->get("order_otp_{$order->id}");

        if ($cached && hash_equals($cached, $otp)) {
            cache()->forget("order_otp_{$order->id}");
            return true;
        }

        return false;
    }

    /**
     * Send OTP via SMS (log-based for now, integrate real SMS provider later).
     */
    public function send(Order $order, string $otp): void
    {
        $phone = $order->customer_phone;

        if (empty($phone)) {
            Log::warning("Cannot send OTP: no phone for order {$order->order_number}");
            return;
        }

        // TODO: Integrate real SMS gateway (e.g., Twilio, GreenWeb, SMS.net)
        Log::info("SMS OTP to {$phone}: Your {$order->order_number} OTP is {$otp}. Valid for 10 minutes.");
    }

    /**
     * Generate and send OTP in one call.
     */
    public function generateAndSend(Order $order): string
    {
        $otp = $this->generate($order);
        $this->send($order, $otp);
        return $otp;
    }
}

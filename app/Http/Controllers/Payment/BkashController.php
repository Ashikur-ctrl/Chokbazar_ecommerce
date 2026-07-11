<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\BkashService;
use Illuminate\Http\Request;

class BkashController extends Controller
{
    protected BkashService $bkash;

    public function __construct(BkashService $bkash)
    {
        $this->bkash = $bkash;
    }

    public function init(Order $order)
    {
        abort_if($order->payment_status === 'paid', 400, 'Order already paid.');

        $result = $this->bkash->createPayment($order);

        if ($result['success']) {
            session(['bkash_payment_id' => $result['payment_id'], 'bkash_order_id' => $order->id]);
            return redirect($result['gateway_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function callback(Request $request)
    {
        $paymentId = session('bkash_payment_id');
        $orderId = session('bkash_order_id');

        if (!$paymentId || !$orderId) {
            return redirect()->route('checkout.index')->with('error', 'Payment session expired.');
        }

        $result = $this->bkash->executePayment($paymentId);

        session()->forget(['bkash_payment_id', 'bkash_order_id']);

        if ($result['success']) {
            $order = Order::findOrFail($orderId);
            $order->update(['payment_status' => 'paid']);
            $order->markAsPaid('bkash');

            return redirect()->route('checkout.success', $order)->with('success', 'bKash payment successful!');
        }

        return redirect()->route('checkout.index')->with('error', 'bKash payment failed.');
    }
}

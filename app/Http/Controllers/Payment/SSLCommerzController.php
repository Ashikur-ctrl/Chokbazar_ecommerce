<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\SSLCommerzService;
use Illuminate\Http\Request;

class SSLCommerzController extends Controller
{
    protected SSLCommerzService $sslcommerz;

    public function __construct(SSLCommerzService $sslcommerz)
    {
        $this->sslcommerz = $sslcommerz;
    }

    public function init(Order $order)
    {
        abort_if($order->payment_status === 'paid', 400, 'Order already paid.');

        $result = $this->sslcommerz->initPayment($order);

        if ($result['success']) {
            return redirect($result['gateway_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function success(Request $request)
    {
        $result = $this->sslcommerz->validatePayment($request->all());

        if ($result['success']) {
            $order = Order::where('order_number', $result['order_number'])->firstOrFail();
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'sslcommerz',
            ]);
            $order->markAsPaid('sslcommerz');

            return redirect()->route('checkout.success', $order)->with('success', 'Payment successful!');
        }

        return redirect()->route('checkout.index')->with('error', 'Payment verification failed.');
    }

    public function fail(Request $request)
    {
        return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('checkout.index')->with('error', 'Payment cancelled.');
    }

    public function ipn(Request $request)
    {
        $result = $this->sslcommerz->validatePayment($request->all());

        if ($result['success']) {
            $order = Order::where('order_number', $result['order_number'])->first();
            if ($order && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);
                $order->markAsPaid('sslcommerz');
            }
        }

        return response('OK', 200);
    }
}

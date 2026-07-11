<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\NagadService;
use Illuminate\Http\Request;

class NagadController extends Controller
{
    protected NagadService $nagad;

    public function __construct(NagadService $nagad)
    {
        $this->nagad = $nagad;
    }

    public function init(Order $order)
    {
        abort_if($order->payment_status === 'paid', 400, 'Order already paid.');

        $result = $this->nagad->createPayment($order);

        if ($result['success']) {
            session(['nagad_order_id' => $result['order_id'], 'nagad_order_ref' => $order->id]);
            return redirect($result['gateway_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function callback(Request $request)
    {
        $orderRef = session('nagad_order_ref');

        if (!$orderRef) {
            return redirect()->route('checkout.index')->with('error', 'Payment session expired.');
        }

        $result = $this->nagad->verifyPayment($request->query());

        session()->forget(['nagad_order_id', 'nagad_order_ref']);

        if ($result['success']) {
            $order = Order::findOrFail($orderRef);
            $order->update(['payment_status' => 'paid']);
            $order->markAsPaid('nagad');

            return redirect()->route('checkout.success', $order)->with('success', 'Nagad payment successful!');
        }

        return redirect()->route('checkout.index')->with('error', 'Nagad payment failed.');
    }
}

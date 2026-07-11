<!DOCTYPE html>
<html><body>
    <h2>Order Confirmed!</h2>
    <p>Dear {{ $order->customer_name }},</p>
    <p>Your order <strong>{{ $order->order_number }}</strong> has been confirmed.</p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#f3f4f6"><th style="padding:8px;text-align:left">Item</th><th style="padding:8px">Qty</th><th style="padding:8px">Price</th></tr>
        @foreach($order->items as $item)
            <tr><td style="padding:8px;border-top:1px solid #e5e7eb">{{ $item->product_name }}</td><td style="padding:8px;border-top:1px solid #e5e7eb;text-align:center">{{ $item->quantity }}</td><td style="padding:8px;border-top:1px solid #e5e7eb;text-align:right">{{ taka($item->total) }}</td></tr>
        @endforeach
    </table>
    <p><strong>Total: {{ taka($order->total_amount) }}</strong></p>
    <p>Shipping to: {{ $order->shipping_address }}</p>
    <p>Payment: {{ ucfirst($order->payment_method) }}</p>
    <p>Track your order: <a href="{{ route('orders.tracking', $order) }}">{{ route('orders.tracking', $order) }}</a></p>
    <p style="color:#6b7280;font-size:12px">Thank you for shopping with us!</p>
</body></html>

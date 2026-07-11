<!DOCTYPE html>
<html><body>
    <h2>New Fulfillment Request</h2>
    <p>Dear {{ $seller->company_name ?? $seller->name }},</p>
    <p>You have received a new fulfillment request: <strong>{{ $fulfillmentRequest->fulfillment_request_number }}</strong></p>
    <p>Order: {{ $fulfillmentRequest->order->order_number }}</p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#f3f4f6"><th style="padding:8px;text-align:left">SKU</th><th style="padding:8px;text-align:left">Product</th><th style="padding:8px">Qty</th><th style="padding:8px">Price</th></tr>
        @foreach($fulfillmentRequest->items as $item)
            <tr><td style="padding:8px;border-top:1px solid #e5e7eb">{{ $item->sku }}</td><td style="padding:8px;border-top:1px solid #e5e7eb">{{ $item->product->name }}</td><td style="padding:8px;border-top:1px solid #e5e7eb;text-align:center">{{ $item->quantity }}</td><td style="padding:8px;border-top:1px solid #e5e7eb;text-align:right">{{ taka($item->price) }}</td></tr>
        @endforeach
    </table>
    <p>Customer: {{ $fulfillmentRequest->order->customer_name }} — {{ $fulfillmentRequest->order->customer_phone }}</p>
    <p>Shipping Address: {{ $fulfillmentRequest->order->shipping_address }}</p>
    <p style="color:#6b7280;font-size:12px">Please log in to your seller dashboard to confirm this request.</p>
</body></html>

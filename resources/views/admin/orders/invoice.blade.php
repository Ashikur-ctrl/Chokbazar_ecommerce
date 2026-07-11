<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white p-8 text-slate-950">
    <div class="mx-auto max-w-4xl">
        <div class="flex items-start justify-between border-b pb-6">
            <div>
                <h1 class="text-3xl font-black">Invoice / Order Slip</h1>
                <p class="mt-2 text-sm text-slate-500">{{ config('app.name', 'E-Commerce') }}</p>
            </div>
            <button onclick="window.print()" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white print:hidden">Print</button>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <h2 class="font-bold">Bill To</h2>
                <p class="mt-2 text-sm">{{ $order->customer_name }}</p>
                <p class="text-sm">{{ $order->customer_email }}</p>
                <p class="text-sm">{{ $order->customer_phone }}</p>
                <p class="mt-2 whitespace-pre-line text-sm">{{ $order->shipping_address }}</p>
            </div>
            <div class="text-sm md:text-right">
                <p><strong>Invoice:</strong> {{ $order->invoice_number }}</p>
                <p><strong>Order:</strong> {{ $order->order_number }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <table class="mt-8 min-w-full border text-sm">
            <thead class="bg-slate-100"><tr><th class="border px-3 py-2 text-left">Product</th><th class="border px-3 py-2">Qty</th><th class="border px-3 py-2">Price</th><th class="border px-3 py-2">Total</th></tr></thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr><td class="border px-3 py-2">{{ $item->product_name }}</td><td class="border px-3 py-2 text-center">{{ $item->quantity }}</td><td class="border px-3 py-2 text-right">{{ taka($item->price) }}</td><td class="border px-3 py-2 text-right">{{ taka($item->total) }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 ml-auto w-72 space-y-2 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>{{ taka($order->subtotal) }}</span></div>
            <div class="flex justify-between"><span>Shipping</span><span>{{ taka($order->shipping_amount) }}</span></div>
            <div class="flex justify-between"><span>Tax</span><span>{{ taka($order->tax_amount) }}</span></div>
            <div class="flex justify-between border-t pt-2 text-lg font-black"><span>Total</span><span>{{ taka($order->total_amount) }}</span></div>
        </div>
    </div>
</body>
</html>

<x-seller-layout>
    <x-slot:title>Order #{{ $fulfillmentRequest->fulfillment_request_number }}</x-slot:title>
    <x-slot:heading>Order Details</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Fulfillment #{{ $fulfillmentRequest->fulfillment_request_number }}</p></x-slot:subheading>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Order Items -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Items</h2>
            </div>
            <div class="p-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-3 font-semibold text-gray-600">Product</th>
                            <th class="text-center pb-3 font-semibold text-gray-600">Qty</th>
                            <th class="text-right pb-3 font-semibold text-gray-600">Price</th>
                            <th class="text-right pb-3 font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fulfillmentRequest->items as $item)
                            <tr class="border-b border-gray-50">
                                <td class="py-3">
                                    <p class="font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $item->sku ?: 'N/A' }}</p>
                                </td>
                                <td class="py-3 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 text-right">{{ taka($item->price) }}</td>
                                <td class="py-3 text-right font-bold">{{ taka($item->quantity * $item->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="pt-4 text-right font-semibold text-gray-700">Total:</td>
                            <td class="pt-4 text-right font-extrabold text-gray-900">{{ taka($fulfillmentRequest->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Order Info & Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Order Info</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $fulfillmentRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $fulfillmentRequest->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $fulfillmentRequest->status === 'shipped' ? 'bg-green-100 text-green-800' : '' }}
                            ">{{ ucfirst($fulfillmentRequest->status) }}</span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Order #</dt>
                        <dd class="font-medium text-gray-900">{{ $fulfillmentRequest->order->order_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Customer</dt>
                        <dd class="font-medium text-gray-900">{{ $fulfillmentRequest->order->customer_name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-900">{{ $fulfillmentRequest->order->customer_phone ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date</dt>
                        <dd class="font-medium text-gray-900">{{ $fulfillmentRequest->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                </dl>
            </div>

            @if($fulfillmentRequest->order->shipping_address)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-2">Shipping Address</h2>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $fulfillmentRequest->order->shipping_address }}</p>
                </div>
            @endif

            @if($fulfillmentRequest->status === 'pending' || $fulfillmentRequest->status === 'confirmed')
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Mark as Shipped</h2>
                    <form method="POST" action="{{ route('seller.orders.mark-shipped', $fulfillmentRequest) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700">Tracking Number</label>
                                <input id="tracking_number" name="tracking_number" type="text"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            </div>
                            <div>
                                <label for="courier_name" class="block text-sm font-medium text-gray-700">Courier</label>
                                <input id="courier_name" name="courier_name" type="text" placeholder="e.g. Pathao, RedX, Steadfast"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                                Mark as Shipped
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($fulfillmentRequest->tracking_number)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-2">Tracking</h2>
                    <p class="text-sm text-gray-600">Tracking: <span class="font-medium text-gray-900">{{ $fulfillmentRequest->tracking_number }}</span></p>
                    @if($fulfillmentRequest->shipped_at)
                        <p class="text-sm text-gray-500 mt-1">Shipped: {{ $fulfillmentRequest->shipped_at->format('d M Y, h:i A') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-seller-layout>

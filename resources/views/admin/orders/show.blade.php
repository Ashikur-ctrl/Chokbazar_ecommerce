<x-admin-layout title="{{ $order->order_number }}">
    <div class="max-w-7xl mx-auto space-y-6">
        @if(session('success'))
            <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
        @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Items</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                            @if($item->product_sku)
                                                <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-900">{{ taka($item->price) }}</td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ taka($item->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-4">Update Status</h3>
                        <form method="POST" action="{{ route('admin-legacy.orders.update-status', $order) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Order status</label>
                                <select id="status" name="status" class="mt-1 w-full rounded-md border-gray-300">
                                    @foreach(['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'returned', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="courier_name" class="block text-sm font-medium text-gray-700">Courier</label>
                                <input id="courier_name" name="courier_name" value="{{ old('courier_name', $order->courier_name) }}" class="mt-1 w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700">Tracking number</label>
                                <input id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" class="mt-1 w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment status</label>
                                <select id="payment_status" name="payment_status" class="mt-1 w-full rounded-md border-gray-300">
                                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                                        <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">Save</button>
                        </form>
                        <a href="{{ route('admin-legacy.orders.invoice', $order) }}" class="mt-3 block rounded-md border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">Print invoice</a>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-4">Customer</h3>
                        <p class="text-sm text-gray-900">{{ $order->customer_name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->customer_email }}</p>
                        @if($order->customer_phone)
                            <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                        @endif
                        <p class="mt-4 text-sm text-gray-700 whitespace-pre-line">{{ $order->shipping_address }}</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-4">Totals</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span>Subtotal</span><span>{{ taka($order->subtotal) }}</span></div>
                            <div class="flex justify-between"><span>Shipping</span><span>{{ taka($order->shipping_amount) }}</span></div>
                            <div class="flex justify-between"><span>Tax</span><span>{{ taka($order->tax_amount) }}</span></div>
                            <div class="flex justify-between border-t pt-2 font-semibold text-gray-900"><span>Total</span><span>{{ taka($order->total_amount) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

<x-admin-layout title="Fulfillment Request {{ $fulfillmentRequest->fulfillment_request_number }}">
    <div class="flex items-center justify-between mb-6">
                @if($fulfillmentRequest->status === 'pending')
                    <form method="POST" action="{{ route('admin.fulfillment.confirm', $fulfillmentRequest) }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg bg-purple-600 px-4 py-2 text-white font-semibold hover:bg-purple-700">
                            Confirm Fulfillment
                        </button>
                    </form>
                @elseif($fulfillmentRequest->status === 'confirmed')
                    <button onclick="document.getElementById('shipModal').showModal()" class="rounded-lg bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">
                        Mark as Shipped
                    </button>
                @endif
                <a href="{{ route('admin.fulfillment.export-csv', $fulfillmentRequest) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-white font-semibold hover:bg-blue-700">
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Status and Details -->
            <div class="mb-6 grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-slate-900">Request Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-slate-600">Status</p>
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $fulfillmentRequest->status === 'pending' ? 'bg-blue-100 text-blue-800' : ($fulfillmentRequest->status === 'confirmed' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ ucfirst($fulfillmentRequest->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Seller</p>
                            <a href="{{ route('admin.sellers.show', $fulfillmentRequest->seller) }}" class="font-semibold text-brand-600 hover:text-brand-700">
                                {{ $fulfillmentRequest->seller->name }}
                            </a>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Total Amount</p>
                            <p class="text-2xl font-bold text-slate-900">৳{{ number_format($fulfillmentRequest->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-slate-900">Order Details</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-slate-600">Order Number</p>
                            <a href="{{ route('admin.fulfillment.order', $fulfillmentRequest->order) }}" class="font-semibold text-brand-600 hover:text-brand-700">
                                {{ $fulfillmentRequest->order->order_number }}
                            </a>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Customer</p>
                            <p class="font-semibold">{{ $fulfillmentRequest->order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Tracking Number</p>
                            <p class="font-semibold">{{ $fulfillmentRequest->tracking_number ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="mb-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Fulfillment Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-slate-900">SKU</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-900">Product Name</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-900">Quantity</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-900">Price</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-900">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fulfillmentRequest->items as $item)
                                <tr class="border-b border-slate-200 hover:bg-slate-50">
                                    <td class="px-4 py-2 font-mono text-slate-600">{{ $item->sku }}</td>
                                    <td class="px-4 py-2">{{ $item->product->name }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2">৳{{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-2 font-semibold">৳{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-slate-300 bg-slate-50">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                <td class="px-4 py-2 font-bold text-slate-900">৳{{ number_format($fulfillmentRequest->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Shipping Address</h3>
                <p class="whitespace-pre-wrap text-slate-600">{{ $fulfillmentRequest->order->shipping_address }}</p>
            </div>
        </div>
    </div>

    <!-- Mark as Shipped Modal -->
    <dialog id="shipModal" class="rounded-lg border border-slate-200 shadow-lg">
        <div class="p-6">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Mark as Shipped</h3>
            <form method="POST" action="{{ route('admin.fulfillment.mark-shipped', $fulfillmentRequest) }}">
                @csrf
                <div class="mb-4">
                    <label for="tracking_number" class="block text-sm font-semibold text-slate-900">Tracking Number (Optional)</label>
                    <input type="text" name="tracking_number" id="tracking_number" 
                           class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">
                        Mark as Shipped
                    </button>
                    <button type="button" onclick="document.getElementById('shipModal').close()" class="rounded-lg bg-slate-200 px-4 py-2 font-semibold text-slate-900 hover:bg-slate-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-admin-layout>

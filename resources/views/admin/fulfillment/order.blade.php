<x-admin-layout title="Order Fulfillment - {{ $order->order_number }}">
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold text-slate-950">Order Fulfillment Status - {{ $order->order_number }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Status Summary -->
            <div class="mb-8 grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Total Requests</p>
                    <p class="text-3xl font-bold text-brand-600">{{ $status['total_requests'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Pending</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $status['pending'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Confirmed</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $status['confirmed'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-600">Shipped</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $status['shipped'] }}</p>
                </div>
            </div>

            <!-- Fulfillment Requests -->
            <div class="space-y-6">
                @forelse($fulfillmentRequests as $request)
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $request->seller->name }}</h3>
                                <p class="text-sm text-slate-600">Request: {{ $request->fulfillment_request_number }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $request->status === 'pending' ? 'bg-blue-100 text-blue-800' : ($request->status === 'confirmed' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="mb-4 border-t border-slate-200 pt-4">
                            <p class="mb-3 font-semibold text-slate-900">Items ({{ $request->items->count() }})</p>
                            <ul class="space-y-2">
                                @foreach($request->items as $item)
                                    <li class="flex items-center justify-between text-sm">
                                        <span>{{ $item->product->name }} ({{ $item->sku }}) × {{ $item->quantity }}</span>
                                        <span class="font-semibold">৳{{ number_format($item->quantity * $item->price, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Total -->
                        <div class="mb-4 border-t border-slate-200 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900">Total Amount:</span>
                                <span class="text-2xl font-bold text-brand-600">৳{{ number_format($request->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <!-- Tracking Number -->
                        @if($request->tracking_number)
                            <div class="mb-4 border-t border-slate-200 pt-4">
                                <p class="text-sm text-slate-600">Tracking Number</p>
                                <p class="font-mono font-semibold text-slate-900">{{ $request->tracking_number }}</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="border-t border-slate-200 pt-4">
                            <a href="{{ route('admin.fulfillment.show', $request) }}" class="text-brand-600 hover:text-brand-700 font-semibold">View Details →</a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                        No fulfillment requests for this order yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>

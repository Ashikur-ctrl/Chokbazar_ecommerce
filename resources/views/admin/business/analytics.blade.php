<x-admin-layout title="Analytics">
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-4">
            <x-stat-card label="Revenue" :value="taka($revenue)" color="emerald" />
            <x-stat-card label="Expenses" :value="taka($expenses)" color="red" />
            <x-stat-card label="Gross Profit" :value="taka($grossProfit)" color="brand" />
            <x-stat-card label="Discounts" :value="taka($discounts)" color="amber" />
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                <h3 class="font-bold text-gray-900 mb-4">Daily Sales (14 days)</h3>
                @if($dailySales->count() > 0)
                    <div class="space-y-3">
                        @php $maxTotal = (float) $dailySales->max('total'); @endphp
                        @foreach($dailySales as $day)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $day->day }}</span>
                                    <span class="font-semibold text-gray-900">{{ taka($day->total) }} &middot; {{ $day->orders }} orders</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600 transition-all" style="width: {{ $maxTotal > 0 ? min(100, ((float)$day->total / $maxTotal) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No sales data yet.</p>
                @endif
            </div>

            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                <h3 class="font-bold text-gray-900 mb-4">Top Selling Products</h3>
                @if($topProducts->count() > 0)
                    <div class="divide-y divide-gray-50">
                        @foreach($topProducts as $product)
                            <div class="flex justify-between py-3 text-sm">
                                <span class="text-gray-700 font-medium">{{ $product->product_name }}</span>
                                <span class="text-gray-500">{{ $product->sold }} sold · <span class="font-semibold text-gray-900">{{ taka($product->revenue) }}</span></span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No product sales yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

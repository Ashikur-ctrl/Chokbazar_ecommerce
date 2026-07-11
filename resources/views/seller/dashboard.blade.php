<x-seller-layout>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:heading>Dashboard</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Welcome back, {{ $seller->company_name }}</p></x-slot:subheading>

    <!-- Stats Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Products</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $productsCount }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ $activeProductsCount }} active</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Orders Pending</p>
            <p class="mt-2 text-3xl font-extrabold text-brand-600">{{ $pendingOrders }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Fulfilled</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $totalFulfilled }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Revenue</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ taka($totalRevenue) }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-2">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                <a href="{{ route('seller.orders.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">View all</a>
            </div>
            <div class="p-6">
                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $fr)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">#{{ $fr->order->order_number ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $fr->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $fr->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $fr->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $fr->status === 'shipped' ? 'bg-green-100 text-green-800' : '' }}
                                    ">{{ ucfirst($fr->status) }}</span>
                                    <p class="mt-1 text-xs text-gray-500">{{ taka($fr->total_amount) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No orders yet.</p>
                @endif
            </div>
        </div>

        <!-- Recent Products -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Recent Products</h2>
                <a href="{{ route('seller.products.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">View all</a>
            </div>
            <div class="p-6">
                @if($recentProducts->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentProducts as $product)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->stock }} in stock</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">{{ taka($product->current_price) }}</p>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">
                        No products yet.
                        <a href="{{ route('seller.products.create') }}" class="text-brand-600 hover:underline">Add your first product</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-seller-layout>

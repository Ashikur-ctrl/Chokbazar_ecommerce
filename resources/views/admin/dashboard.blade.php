<x-admin-layout title="Dashboard">
    <div class="space-y-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <x-stat-card label="Total Orders" :value="$stats['orders']" color="brand">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></x-slot>
            </x-stat-card>
            <x-stat-card label="Pending Orders" :value="$stats['pending_orders']" color="amber">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
            </x-stat-card>
            <x-stat-card label="Revenue" :value="taka($stats['revenue'])" color="emerald">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
            </x-stat-card>
            <x-stat-card label="Products" :value="$stats['products']" color="secondary">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></x-slot>
            </x-stat-card>
            <x-stat-card label="Low Stock" :value="$stats['low_stock']" color="red">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></x-slot>
            </x-stat-card>
            <x-stat-card label="Customers" :value="$stats['customers']" color="purple">
                <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot>
            </x-stat-card>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-animate>
            @php $links = [
                ['Analytics', 'admin.analytics', 'analytics'],
                ['Inventory', 'admin.inventory', 'inventory'],
                ['Customers', 'admin.customers', 'customers'],
                ['Expenses', 'admin.expenses', 'expenses'],
                ['Reports', 'admin.reports', 'reports'],
                ['Notifications', 'admin.notifications', 'notifications'],
                ['Sellers', 'admin.sellers.index', 'sellers'],
                ['Fulfillment', 'admin.fulfillment.index', 'fulfillment'],
            ]; @endphp
            @foreach($links as $link)
                <a href="{{ route($link[1]) }}"
                   class="rounded-card border border-gray-100 bg-white p-5 shadow-card hover:shadow-card-hover transition-all duration-200 hover:-translate-y-0.5">
                    <p class="text-sm font-semibold text-gray-900">{{ $link[0] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($link[2]) }} &rarr;</p>
                </a>
            @endforeach
        </div>

        <!-- Recent Orders + Low Stock -->
        <div class="grid gap-8 xl:grid-cols-2">
            <div class="rounded-card border border-gray-100 bg-white shadow-card">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Recent Orders</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $order->customer_name }} &middot; {{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 text-sm">{{ taka($order->total_amount) }}</p>
                                <x-badge :variant="match($order->status) { 'pending' => 'warning', 'processing' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger', default => 'neutral' }" size="xs">{{ ucfirst($order->status) }}</x-badge>
                            </div>
                        </a>
                    @empty
                        <p class="p-6 text-sm text-gray-500 text-center">No orders yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-card border border-gray-100 bg-white shadow-card">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Low Stock Products</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($lowStockProducts as $product)
                        <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center justify-between p-4 hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <x-badge variant="danger" size="xs">{{ $product->stock }} left</x-badge>
                        </a>
                    @empty
                        <p class="p-6 text-sm text-gray-500 text-center">No low-stock products.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

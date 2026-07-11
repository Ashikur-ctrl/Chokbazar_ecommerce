<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-brand-600">My Orders</p>
            <h2 class="text-2xl font-extrabold text-gray-900">Orders</h2>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                                <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                                <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Payment</th>
                                <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('orders.show', $order) }}" class="font-semibold text-brand-600 hover:text-brand-700">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="px-5 py-4 text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="px-5 py-4">
                                            <x-badge :variant="match($order->status) { 'pending' => 'warning', 'processing' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger', default => 'neutral' }">{{ ucfirst($order->status) }}</x-badge>
                                        </td>
                                        <td class="px-5 py-4">
                                            <x-badge :variant="$order->payment_status === 'paid' ? 'success' : 'warning'">{{ ucfirst($order->payment_status) }}</x-badge>
                                        </td>
                                        <td class="px-5 py-4 text-right font-bold text-gray-900">{{ taka($order->total_amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($orders->hasPages())<div class="p-6 border-t">{{ $orders->links() }}</div>@endif
                </div>
            @else
                <x-empty-state title="No orders yet" description="Start shopping to see your orders here." :actionUrl="route('shop.index')" actionLabel="Start Shopping" />
            @endif
        </div>
    </div>
</x-app-layout>

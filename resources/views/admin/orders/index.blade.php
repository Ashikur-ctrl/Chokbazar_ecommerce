<x-admin-layout title="Orders">
    <div class="space-y-6">
        <div class="rounded-card border border-gray-100 bg-white shadow-card">
            <div class="p-6 border-b border-gray-100">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">All statuses</option>
                        @foreach(['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'returned', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <select name="payment_status" class="rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">All payments</option>
                        @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/80">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Payment</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin-legacy.orders.show', $order) }}" class="font-semibold text-brand-600 hover:text-brand-700">{{ $order->order_number }}</a>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $order->customer_name }}<br><span class="text-xs text-gray-500">{{ $order->customer_email }}</span></td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="match($order->status) { 'pending' => 'warning', 'confirmed' => 'info', 'processing' => 'info', 'packed' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'returned' => 'warning', 'cancelled' => 'danger', default => 'neutral' }">{{ ucfirst($order->status) }}</x-badge>
                                </td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="$order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning')">{{ ucfirst($order->payment_status) }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900">{{ taka($order->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-500">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>

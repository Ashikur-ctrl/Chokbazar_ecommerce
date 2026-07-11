<x-admin-layout title="Fulfillment Requests">
    <div class="space-y-6">
        <!-- Status Filter Tabs -->
        <div class="flex gap-2">
            <a href="{{ route('admin.fulfillment.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ !request('status') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">All</a>
            @foreach(['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'success'] as $status => $variant)
                <a href="{{ route('admin.fulfillment.index', ['status' => $status]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ request('status') === $status ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ ucfirst($status) }}</a>
            @endforeach
        </div>

        <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Request #</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Seller</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Items</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($fulfillmentRequests as $fr)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $fr->fulfillment_request_number }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $fr->order->order_number }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $fr->seller->name }}</td>
                                <td class="px-5 py-4 text-center">{{ $fr->items->count() }}</td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900">{{ taka($fr->total_amount) }}</td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="match($fr->status) { 'pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'success', default => 'neutral' }">{{ ucfirst($fr->status) }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.fulfillment.show', $fr) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-500">No fulfillment requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($fulfillmentRequests->hasPages())<div class="p-6 border-t">{{ $fulfillmentRequests->links() }}</div>@endif
        </div>
    </div>
</x-admin-layout>

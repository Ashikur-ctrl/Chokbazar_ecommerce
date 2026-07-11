<x-admin-layout title="Customers">
    <div class="rounded-card border border-gray-100 bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Orders</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Total Spent</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Segment</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $customer)
                        @php $total = (float) ($customer->orders_sum_total_amount ?? 0); @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                            </td>
                            <td class="px-5 py-4 text-center font-semibold">{{ $customer->orders_count }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ taka($total) }}</td>
                            <td class="px-5 py-4">
                                @php $segment = $customer->orders_count >= 5 ? 'VIP' : ($customer->orders_count >= 2 ? 'Repeat' : 'New'); @endphp
                                <x-badge :variant="$segment === 'VIP' ? 'brand' : ($segment === 'Repeat' ? 'info' : 'neutral')">{{ $segment }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())<div class="p-6 border-t">{{ $customers->links() }}</div>@endif
    </div>
</x-admin-layout>

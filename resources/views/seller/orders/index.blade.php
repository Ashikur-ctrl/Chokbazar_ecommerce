<x-seller-layout>
    <x-slot:title>Orders</x-slot:title>
    <x-slot:heading>Orders</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Fulfillment requests assigned to you</p></x-slot:subheading>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <form method="GET" class="flex gap-3">
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">All status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                    <option value="shipped" @selected(request('status') === 'shipped')>Shipped</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">Filter</button>
                @if(request('status'))
                    <a href="{{ route('seller.orders.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            @if($orders->count() > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Order #</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Customer</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Total</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Date</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $fr)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $fr->fulfillment_request_number }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900">{{ $fr->order->customer_name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ taka($fr->total_amount) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $fr->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $fr->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $fr->status === 'shipped' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $fr->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ ucfirst($fr->status) }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $fr->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('seller.orders.show', $fr) }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <p class="text-gray-500">No orders assigned to you yet.</p>
                </div>
            @endif
        </div>

        @if($orders->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-seller-layout>

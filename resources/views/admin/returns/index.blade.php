<x-admin-layout title="Return Requests">
    <div class="space-y-6">
        <!-- Status Filter Tabs -->
        <div class="flex gap-2">
            <a href="{{ route('admin.returns.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ !request('status') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">All</a>
            @foreach(['pending', 'approved', 'rejected', 'refunded'] as $status)
                <a href="{{ route('admin.returns.index', ['status' => $status]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ request('status') === $status ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ ucfirst($status) }}</a>
            @endforeach
        </div>

        <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/80">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Request</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Reason</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($returnRequests as $return)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 font-medium text-gray-900">#{{ $return->id }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.orders.show', $return->order) }}" class="font-semibold text-brand-600 hover:text-brand-700">{{ $return->order->order_number }}</a>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $return->user->name ?? 'N/A' }}<br><span class="text-xs text-gray-500">{{ $return->user->email ?? '' }}</span></td>
                                <td class="px-5 py-4 text-gray-600 max-w-xs truncate">{{ $return->reason }}</td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="match($return->status) {
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'refunded' => 'success',
                                        default => 'neutral'
                                    }">{{ ucfirst($return->status) }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs">{{ $return->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if($return->status === 'pending')
                                        <div class="flex gap-2 justify-end">
                                            <form method="POST" action="{{ route('admin.returns.approve', $return) }}" class="inline">
                                                @csrf
                                                <button class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.returns.reject', $return) }}" class="inline" onsubmit="var note = prompt('Rejection reason:'); if(note === null) return false; this.querySelector('[name=admin_note]').value = note; return true;">
                                                @csrf
                                                <input type="hidden" name="admin_note" value="">
                                                <button class="text-sm font-semibold text-red-600 hover:text-red-700 transition-colors">Reject</button>
                                            </form>
                                        </div>
                                    @elseif($return->status === 'approved')
                                        <form method="POST" action="{{ route('admin.returns.refund', $return) }}" class="inline">
                                            @csrf
                                            <button class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">Process Refund</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-500">No return requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($returnRequests->hasPages())
                <div class="p-6 border-t border-gray-100">{{ $returnRequests->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>

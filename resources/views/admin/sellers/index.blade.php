<x-admin-layout title="Sellers">
    <div class="space-y-6">
        @if(session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $sellers->total() }} total sellers</p>
            <a href="{{ route('admin.sellers.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">
                + Add Seller
            </a>
        </div>

        <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Seller</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Company</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Method</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Commission</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Verified</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($sellers as $seller)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900">{{ $seller->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $seller->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $seller->company_name ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    <x-badge variant="info" size="xs">{{ ucfirst($seller->fulfillment_method) }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $seller->commission_percentage }}%</td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="$seller->is_active ? 'success' : 'neutral'">{{ $seller->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                </td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="match($seller->verification_status) { 'verified' => 'success', 'pending' => 'warning', 'rejected' => 'danger', default => 'neutral' }">{{ ucfirst($seller->verification_status ?? 'pending') }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.sellers.show', $seller) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View</a>
                                    <a href="{{ route('admin.sellers.edit', $seller) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-700">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-500">No sellers found. <a href="{{ route('admin.sellers.create') }}" class="text-brand-600 hover:underline">Create one</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sellers->hasPages())<div class="p-6 border-t">{{ $sellers->links() }}</div>@endif
        </div>
    </div>
</x-admin-layout>

<x-admin-layout title="Pending Seller Approvals">
    <div class="space-y-6">
        @if(session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

            @if($sellers->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Company</th>
                                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Contact</th>
                                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Type</th>
                                    <th class="text-left px-6 py-3 font-semibold text-gray-600">Registered</th>
                                    <th class="text-right px-6 py-3 font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellers as $seller)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-900">{{ $seller->company_name ?: $seller->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $seller->name }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900">{{ $seller->email }}</p>
                                            @if($seller->phone)
                                                <p class="text-xs text-gray-500">{{ $seller->phone }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($seller->business_type)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 capitalize">{{ $seller->business_type }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">{{ $seller->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('admin.sellers.show', $seller) }}" class="text-sm text-indigo-600 hover:underline">View</a>
                                            <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" class="inline">
                                                @csrf
                                                <button class="text-sm text-emerald-600 hover:underline font-medium">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}" class="inline" onsubmit="return confirm('Reject this seller?')">
                                                @csrf
                                                <button class="text-sm text-red-600 hover:underline">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($sellers->hasPages())
                        <div class="p-6 border-t">
                            {{ $sellers->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-gray-500">No pending seller approvals.</p>
                </div>
            @endif
    </div>
</x-admin-layout>

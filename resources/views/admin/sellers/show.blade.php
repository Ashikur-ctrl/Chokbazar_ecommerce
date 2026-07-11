<x-admin-layout title="Seller: {{ $seller->name }}">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ $seller->name }}</h2>
                <p class="text-sm text-gray-500">{{ $seller->company_name ?? 'No company' }}</p>
            </div>
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Seller Details -->
            <div class="mb-8 grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-slate-900">Contact Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-slate-600">Email</p>
                            <p class="font-semibold text-slate-900">{{ $seller->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Phone</p>
                            <p class="font-semibold text-slate-900">{{ $seller->phone ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Company</p>
                            <p class="font-semibold text-slate-900">{{ $seller->company_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Fulfillment Method</p>
                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                {{ ucfirst($seller->fulfillment_method) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-slate-900">Settings</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-slate-600">Verification</p>
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                {{ $seller->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $seller->verification_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $seller->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            ">{{ ucfirst($seller->verification_status ?? 'pending') }}</span>
                            @if($seller->verification_status === 'pending')
                                <div class="mt-2 flex gap-2">
                                    <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" class="inline">
                                        @csrf
                                        <button class="text-sm text-emerald-600 hover:underline font-medium">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}" class="inline">
                                        @csrf
                                        <button class="text-sm text-red-600 hover:underline">Reject</button>
                                    </form>
                                    <a href="{{ route('admin.sellers.documents', $seller) }}" class="text-sm text-indigo-600 hover:underline">Documents</a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Commission Percentage</p>
                            <p class="font-semibold text-slate-900">{{ $seller->commission_percentage }}%</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">Status</p>
                            <span class="inline-flex rounded-full {{ $seller->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }} px-3 py-1 text-sm font-semibold">
                                {{ $seller->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600">API Key</p>
                            <div class="mt-1 flex items-center gap-2">
                                <code class="text-xs font-mono text-slate-600">{{ substr($seller->api_key ?? '', 0, 20) }}...</code>
                                <form method="POST" action="{{ route('admin.sellers.regenerate-api-key', $seller) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-700 text-sm font-semibold">Regenerate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mb-8 grid gap-6 md:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm text-center">
                    <p class="text-sm text-slate-600">Total Orders</p>
                    <p class="text-4xl font-bold text-orange-600">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm text-center">
                    <p class="text-sm text-slate-600">Pending</p>
                    <p class="text-4xl font-bold text-blue-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm text-center">
                    <p class="text-sm text-slate-600">Confirmed</p>
                    <p class="text-4xl font-bold text-purple-600">{{ $stats['confirmed'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm text-center">
                    <p class="text-sm text-slate-600">Shipped</p>
                    <p class="text-4xl font-bold text-emerald-600">{{ $stats['shipped'] }}</p>
                </div>
            </div>

            <!-- Revenue -->
            <div class="mb-8 grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-orange-600">৳{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-600">Last 30 Days Revenue</p>
                    <p class="text-3xl font-bold text-blue-600">৳{{ number_format($stats['last_30_days_revenue'], 2) }}</p>
                </div>
            </div>

            <!-- Products -->
            <div class="mb-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Products ({{ $seller->products->count() }})</h3>
                @if($seller->products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-slate-200 bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Name</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">SKU</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Price</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seller->products as $product)
                                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                                        <td class="px-4 py-2">{{ $product->name }}</td>
                                        <td class="px-4 py-2">{{ $product->sku }}</td>
                                        <td class="px-4 py-2">৳{{ $product->price }}</td>
                                        <td class="px-4 py-2">{{ $product->stock }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-slate-600">No products assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

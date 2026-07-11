<x-admin-layout title="Inventory">
    <div class="space-y-6">
        <!-- Low Stock -->
        <div class="rounded-card border border-gray-100 bg-white shadow-card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Low Stock Alerts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Stock</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Threshold</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($lowStockProducts as $p)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $p->category->name ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-right"><x-badge variant="danger">{{ $p->stock }}</x-badge></td>
                                <td class="px-5 py-4 text-right text-gray-600">{{ $p->low_stock_threshold }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">No low stock products.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lowStockProducts->hasPages())<div class="p-6 border-t">{{ $lowStockProducts->links() }}</div>@endif
        </div>

        <!-- Dead Stock -->
        <div class="rounded-card border border-gray-100 bg-white shadow-card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Dead Stock (No orders)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Stock</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Valuation</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($deadStockProducts as $p)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-5 py-4 text-right">{{ $p->stock }}</td>
                                <td class="px-5 py-4 text-right font-semibold">{{ taka($p->stock * ($p->cost_price ?? 0)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-12 text-center text-gray-500">No dead stock.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>

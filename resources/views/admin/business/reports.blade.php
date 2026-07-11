<x-admin-layout title="Reports">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['Sales Report', 'sales', 'Lists all orders with customer, status, and totals.'],
            ['Customers', 'customers', 'Customer names, emails, order count, and total spent.'],
            ['Products', 'products', 'Product names, SKUs, prices, stock, and status.'],
            ['Inventory', 'inventory', 'Stock levels, thresholds, and valuation for each product.'],
            ['Expenses', 'expenses', 'All expenses with dates, categories, and amounts.'],
        ] as $report)
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card hover:shadow-card-hover transition-all duration-200">
                <h3 class="font-bold text-gray-900">{{ $report[0] }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ $report[2] }}</p>
                <a href="{{ route('admin.reports.export', $report[1]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download CSV
                </a>
            </div>
        @endforeach
    </div>
</x-admin-layout>

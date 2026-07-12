<x-admin-layout title="Expenses">
    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Add Expense Form -->
        <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
            <h3 class="font-bold text-gray-900 mb-4">Add Expense</h3>
            <form method="POST" action="{{ route('admin-legacy.expenses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" required class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach(['Utilities', 'Rent', 'Salaries', 'Marketing', 'Shipping', 'Supplies', 'Maintenance', 'Other'] as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" required class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount (৳)</label>
                    <input type="number" step="0.01" name="amount" required class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="spent_on" required class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <input type="text" name="payment_method" class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Add Expense</button>
            </form>
        </div>

        <!-- Category Totals + Recent -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                <h3 class="font-bold text-gray-900 mb-4">Category Totals</h3>
                <div class="divide-y divide-gray-50">
                    @forelse($categoryTotals as $cat)
                        <div class="flex justify-between py-2.5 text-sm">
                            <span class="text-gray-700">{{ $cat->category }}</span>
                            <span class="font-semibold text-gray-900">{{ taka($cat->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No expenses recorded.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-card border border-gray-100 bg-white shadow-card">
                <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Recent Expenses</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-gray-100 bg-gray-50/80">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Title</th>
                            <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($expenses as $e)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3.5 text-gray-600">{{ $e->spent_on?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $e->title }}</td>
                                    <td class="px-5 py-3.5"><x-badge variant="neutral">{{ $e->category }}</x-badge></td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">{{ taka($e->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">No expenses yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($expenses->hasPages())<div class="p-6 border-t">{{ $expenses->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-admin-layout>

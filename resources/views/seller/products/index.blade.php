<x-seller-layout>
    <x-slot:title>My Products</x-slot:title>
    <x-slot:heading>My Products</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Manage your product catalog</p></x-slot:subheading>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 sm:w-64">
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="low_stock" @selected(request('status') === 'low_stock')>Low Stock</option>
                </select>
                <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white hover:bg-orange-700">Filter</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('seller.products.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Clear</a>
                @endif
            </form>
            <a href="{{ route('seller.products.create') }}" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white hover:bg-orange-700 whitespace-nowrap">+ Add Product</a>
        </div>

        <div class="overflow-x-auto">
            @if($products->count() > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Product</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Price</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Stock</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Type</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->category)
                                        <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ taka($product->current_price) }}</p>
                                    @if($product->is_on_sale)
                                        <p class="text-xs text-gray-500 line-through">{{ taka($product->price) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $product->stock > $product->low_stock_threshold ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->is_b2b_only)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-700">B2B Only</span>
                                    @elseif($product->is_wholesale)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-700">Wholesale</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">Retail</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('seller.products.edit', $product) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="inline ml-3" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-700 text-sm font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <p class="text-gray-500">No products found.</p>
                    <a href="{{ route('seller.products.create') }}" class="mt-2 inline-block text-orange-600 hover:underline text-sm font-medium">Add your first product</a>
                </div>
            @endif
        </div>

        @if($products->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-seller-layout>

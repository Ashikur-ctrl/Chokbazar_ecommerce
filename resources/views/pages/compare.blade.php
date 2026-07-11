<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-brand-600">Product comparison</p>
                <h2 class="text-2xl font-extrabold text-gray-900">Compare Products</h2>
            </div>
            <form method="POST" action="{{ route('compare.clear') }}">
                @csrf @method('DELETE')
                <button class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 hover:text-brand-600 hover:border-brand-200 transition-colors">Clear All</button>
            </form>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @php($products = $products->isNotEmpty() ? $products : \App\Models\Product::whereIn('id', session('compare', []))->get())
            @if($products->isEmpty())
                <x-empty-state title="No products selected for comparison" :actionUrl="route('shop.index')" actionLabel="Browse Products" />
            @else
                <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <th class="w-44 bg-gray-50/80 px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                                @foreach($products as $product)
                                    <td class="px-5 py-4 align-top">
                                        <a href="{{ route('shop.product', $product) }}" class="font-bold text-gray-900 hover:text-brand-600 transition-colors">{{ $product->name }}</a>
                                        <p class="mt-1 text-lg font-black text-brand-600">{{ taka($product->current_price) }}</p>
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="bg-gray-50/80 px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
                                @foreach($products as $product)
                                    <td class="px-5 py-4 text-gray-700">{{ $product->category->name ?? 'N/A' }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="bg-gray-50/80 px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Stock</th>
                                @foreach($products as $product)
                                    <td class="px-5 py-4">
                                        <span class="{{ $product->stock > 0 ? 'text-emerald-600' : 'text-red-600' }} font-semibold">{{ $product->stock > 0 ? $product->stock . ' available' : 'Out of stock' }}</span>
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="bg-gray-50/80 px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rating</th>
                                @foreach($products as $product)
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                            <span class="text-xs text-gray-400 ml-1">{{ $product->average_rating ?: '0.0' }}</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

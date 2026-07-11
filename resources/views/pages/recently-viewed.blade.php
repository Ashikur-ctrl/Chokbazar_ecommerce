<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-brand-600">Browsing history</p>
            <h2 class="text-2xl font-extrabold text-gray-900">Recently Viewed Products</h2>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4" data-animate>
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <x-empty-state title="No recently viewed products" description="Start browsing to see your history here." :actionUrl="route('shop.index')" actionLabel="Browse Products" />
            @endif
        </div>
    </div>
</x-app-layout>

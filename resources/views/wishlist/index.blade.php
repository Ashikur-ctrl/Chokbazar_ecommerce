<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-brand-600">Wishlist</p>
            <h2 class="text-2xl font-extrabold text-gray-900">My Wishlist</h2>
        </div>
    </x-slot>

    <div class="bg-gray-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif

            @if($items->count() > 0)
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-animate>
                    @foreach($items as $item)
                        <x-product-card :product="$item->product" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $items->links() }}</div>
            @else
                <x-empty-state title="Your wishlist is empty" description="Save products you love to your wishlist!" :actionUrl="route('shop.index')" actionLabel="Start Shopping" icon="heart" />
            @endif
        </div>
    </div>
</x-app-layout>

@props(['product', 'wishlisted' => false, 'compared' => false])

<article {{ $attributes->merge(['class' => 'group rounded-card border border-gray-100 bg-white shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-0.5 overflow-hidden']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
        <a href="{{ route('shop.product', $product) }}">
            @if($product->image)
                <img data-src="{{ asset('storage/' . $product->image) }}"
                     src="{{ asset('storage/' . $product->image) }}"
                     alt="{{ $product->name }}"
                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                     loading="lazy">
            @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-secondary-50">
                    <span class="text-5xl font-black text-brand-200">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                </div>
            @endif
        </a>

        @if($product->is_on_sale)
            <x-badge variant="danger" size="xs" class="absolute left-3 top-3 shadow-sm">
                {{ $product->discount_percentage }}% OFF
            </x-badge>
        @endif
        @if(isset($product->stock) && $product->stock > 0 && $product->stock <= 5)
            <x-badge variant="warning" size="xs" class="absolute right-3 top-3 shadow-sm">
                Only {{ $product->stock }} left
            </x-badge>
        @endif
        @if(isset($product->stock) && $product->stock <= 0)
            <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center">
                <x-badge variant="neutral" size="md">Sold Out</x-badge>
            </div>
        @endif
    </div>

    <div class="p-4 space-y-2">
        <p class="text-xs font-semibold uppercase tracking-wider text-secondary-600">
            {{ $product->category->name ?? 'Uncategorized' }}
        </p>

        <h3 class="font-semibold text-gray-900 line-clamp-2 min-h-[2.5rem]">
            <a href="{{ route('shop.product', $product) }}" class="hover:text-brand-600 transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        @if(isset($product->average_rating))
        <div class="flex items-center gap-1 text-sm">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ $product->reviews_count ?? $product->wishlists_count ?? 0 }})</span>
        </div>
        @endif

        <div class="flex items-center gap-2">
            <span class="text-lg font-black text-brand-600">{{ taka($product->current_price) }}</span>
            @if($product->is_on_sale)
                <span class="text-sm text-gray-400 line-through">{{ taka($product->price) }}</span>
            @endif
        </div>

        <div class="flex gap-2 pt-1">
            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        @disabled(!isset($product->stock) || $product->stock <= 0)
                        class="w-full rounded-lg bg-gradient-to-r from-brand-600 to-brand-700 px-3 py-2.5 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 disabled:from-gray-300 disabled:to-gray-300 transition-all duration-200 active:scale-[0.98]">
                    {{ isset($product->stock) && $product->stock > 0 ? 'Add to Cart' : 'Sold Out' }}
                </button>
            </form>
            <button data-action="wishlist" data-id="{{ $product->id }}"
                    class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-400 hover:text-rose-500 hover:border-rose-200 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="{{ $wishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        @auth
            @php $isCompare = in_array($product->id, session('compare', [])); @endphp
            <form method="POST" action="{{ route('compare.add', $product) }}">
                @csrf
                <button class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold {{ $isCompare ? 'text-emerald-700 border-emerald-200 bg-emerald-50' : 'text-gray-500 hover:text-brand-600 hover:border-brand-200' }} transition-colors">
                    {{ $isCompare ? 'Added to Compare' : '+ Compare' }}
                </button>
            </form>
        @endauth
    </div>
</article>

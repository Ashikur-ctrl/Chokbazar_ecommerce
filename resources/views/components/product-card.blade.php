@props(['product', 'wishlisted' => false])

<div
    x-data="{ loaded: false, imgError: false }"
    x-init="loaded = true"
    class="group relative rounded-xl bg-white border border-gray-100/80 overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-brand-600/5 hover:-translate-y-0.5"
>
    {{-- Image --}}
    <a href="{{ route('shop.product', $product) }}" class="block aspect-[4/3] overflow-hidden bg-gradient-to-br from-brand-50 to-brand-50/50 relative">
        @if ($product->image)
            <img
                src="{{ asset('storage/' . $product->image) }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover transition-all duration-500 group-hover:scale-105"
                loading="lazy"
                onerror="this.parentElement.innerHTML = '<div class=\'flex h-full w-full items-center justify-center text-4xl font-bold text-brand-200\'>{{ mb_substr($product->name, 0, 1) }}</div>'"
            />
        @else
            <div class="flex h-full w-full items-center justify-center">
                <span class="text-5xl font-bold text-brand-200">{{ mb_substr($product->name, 0, 1) }}</span>
            </div>
        @endif

        {{-- Price badge --}}
        @if ($product->is_on_sale)
            <div class="absolute top-2 left-2 bg-amber-400 text-[#1a1a1a] text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                Sale
            </div>
        @endif

        {{-- Wishlist button --}}
        @auth
            <form method="POST" action="{{ $wishlisted ? route('wishlist.destroy', auth()->user()->wishlists()->where('product_id', $product->id)->first()) : route('wishlist.toggle', $product) }}" class="absolute top-2 right-2">
                @csrf
                @if($wishlisted) @method('DELETE') @endif
                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/80 backdrop-blur-sm shadow-sm hover:bg-white transition-all" title="{{ $wishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <svg class="w-4 h-4 {{ $wishlisted ? 'text-red-500 fill-red-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </form>
        @endauth

        {{-- Quick add overlay on hover --}}
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/40 to-transparent h-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
            @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full rounded-lg bg-white/95 backdrop-blur-sm px-3 py-2 text-xs font-bold text-[#1a1a1a] hover:bg-white transition-colors shadow-lg">
                        Add to Cart
                    </button>
                </form>
            @else
                <span class="w-full rounded-lg bg-white/80 backdrop-blur-sm px-3 py-2 text-xs font-medium text-gray-500 text-center">
                    Out of Stock
                </span>
            @endif
        </div>
    </a>

    {{-- Details --}}
    <div class="p-4">
        {{-- Category --}}
        @if($product->category)
            <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="text-[10px] font-semibold uppercase tracking-wider text-brand-600 hover:text-brand-700 transition-colors">
                {{ $product->category->name }}
            </a>
        @endif

        {{-- Name --}}
        <a href="{{ route('shop.product', $product) }}" class="mt-1 block text-sm font-medium text-[#1a1a1a] hover:text-brand-600 transition-colors leading-snug line-clamp-2">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        @if($product->average_rating > 0)
            <div class="mt-1.5 flex items-center gap-1.5">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3.5 h-3.5 {{ $i <= round($product->average_rating) ? 'text-[#d4a853]' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-500">({{ $product->reviews_count ?? 0 }})</span>
            </div>
        @endif

        {{-- Price — local vs import --}}
        @php $mode = session('sourcing_mode', 'local'); @endphp

        @if ($mode === 'import' && $product->is_importable)
            <div class="mt-2">
                <div class="flex items-baseline gap-1">
                    <span class="font-mono text-lg font-bold text-blue-600">{{ $product->fob_price_formatted }}</span>
                    <span class="text-[10px] text-gray-400 font-medium uppercase">FOB</span>
                </div>
                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-400">
                    <span>MOQ: {{ $product->moq ?? 100 }}</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ $product->lead_time_days ?? 30 }} days</span>
                </div>
            </div>
        @elseif ($mode === 'import' && !$product->is_importable)
            <div class="mt-2">
                <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-500">Local stock only</span>
            </div>
        @else
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-mono text-lg font-bold text-brand-600">{{ taka($product->current_price) }}</span>
                @if ($product->is_on_sale && $product->sale_price < $product->price)
                    <span class="font-mono text-sm text-gray-400 line-through">{{ taka($product->price) }}</span>
                @endif
            </div>
        @endif
    </div>
</div>

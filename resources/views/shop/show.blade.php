<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $productSeo = \App\Services\SeoHelper::make()->forProduct($product); @endphp
    <title>{{ $productSeo->getTitle() }}</title>
    {!! $productSeo->renderMeta() !!}
    {!! $productSeo->renderJsonLd() !!}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="min-h-screen">
        <x-shop-header />

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('shop.index') }}" class="hover:text-brand-600 transition-colors">Shop</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if($product->category)
                    <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="hover:text-brand-600 transition-colors">{{ $product->category->name }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
                <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
            </nav>

            <!-- Alerts -->
            @if(session('success'))
                <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert variant="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <!-- Product Detail -->
            <div class="grid gap-8 lg:grid-cols-[1fr_1.1fr]">
                <!-- Left: Gallery -->
                <div x-data="productGallery({ images: {{ json_encode($product->images->map(fn($img) => asset('storage/' . $img->image_path))->values()) }} })"
                     class="space-y-4">
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-card">
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-brand-50 p-4">
                            @if($product->image)
                                <img :src="currentImage || '{{ asset('storage/' . $product->image) }}'"
                                     alt="{{ $product->name }}"
                                     class="h-full w-full object-contain transition-opacity duration-300"
                                     @click="openLightbox()" style="cursor:zoom-in">
                            @else
                                <div class="text-8xl font-black text-brand-200">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
                            @endif
                        </div>
                        @if($product->is_on_sale)
                            <x-badge variant="danger" size="md" class="absolute left-4 top-4 shadow-sm">{{ $product->discount_percentage }}% OFF</x-badge>
                        @endif
                    </div>

                    @if($product->images && $product->images->count() > 0)
                        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                            <button @click="select(0)"
                                    class="shrink-0 w-20 h-20 rounded-xl border-2 overflow-hidden transition-all duration-200"
                                    :class="currentIndex === 0 ? 'border-brand-500 ring-2 ring-brand-100' : 'border-gray-200 hover:border-gray-300'">
                                <x-lazy-image src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full" />
                            </button>
                            @foreach($product->images as $index => $image)
                                <button @click="select({{ $index }})"
                                        class="shrink-0 w-20 h-20 rounded-xl border-2 overflow-hidden transition-all duration-200"
                                        :class="currentIndex === {{ $index }} ? 'border-brand-500 ring-2 ring-brand-100' : 'border-gray-200 hover:border-gray-300'">
                                    <x-lazy-image src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text ?: $product->name }}" class="h-full w-full" />
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Lightbox -->
                    <template x-teleport="body">
                        <div x-show="lightboxOpen" @keydown="handleKeydown" tabindex="0"
                             class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100" @click="closeLightbox()">
                            <button @click="closeLightbox()" class="absolute top-4 right-4 text-white/70 hover:text-white z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <button @click.stop="prev()" class="absolute left-4 text-white/70 hover:text-white z-10">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <img :src="currentImage" class="max-h-[85vh] max-w-[85vw] object-contain" @click.stop>
                            <button @click.stop="next()" class="absolute right-4 text-white/70 hover:text-white z-10">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Right: Product Info -->
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">{{ $product->name }}</h1>

                        <div class="mt-3 flex items-center gap-4">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-1 text-sm text-gray-500">({{ $product->reviews_count ?? 0 }} reviews)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl font-black text-brand-600">{{ taka($product->current_price) }}</span>
                        @if($product->is_on_sale)
                            <span class="text-xl text-gray-400 line-through">{{ taka($product->price) }}</span>
                            <x-badge variant="danger">Save {{ taka($product->price - $product->current_price) }}</x-badge>
                        @endif
                    </div>

                    <!-- Short Description -->
                    @if($product->short_description)
                        <p class="text-base text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
                    @endif

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-gray-50 p-4 text-center">
                            <p class="text-xs text-gray-500">Stock</p>
                            <p class="mt-1 text-lg font-bold {{ $product->stock > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $product->stock > 0 ? $product->stock . ' units' : 'Sold out' }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 text-center">
                            <p class="text-xs text-gray-500">SKU</p>
                            <p class="mt-1 text-sm font-bold text-gray-900 truncate">{{ $product->sku ?: 'N/A' }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 text-center">
                            <p class="text-xs text-gray-500">Delivery</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">3-7 days</p>
                        </div>
                    </div>

                    <!-- Add to Cart -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-card">
                        <div class="flex items-center gap-4">
                            <x-quantity-input name="quantity" :min="max(1, $product->moq)" :max="$product->stock" />
                            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="cart-quantity" value="1">
                                <button type="submit" @disabled($product->stock <= 0)
                                        class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3.5 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 disabled:from-gray-300 disabled:to-gray-300 transition-all duration-200 active:scale-[0.98] shadow-card">
                                    {{ $product->stock > 0 ? 'Add to Cart' : 'Sold Out' }}
                                </button>
                            </form>
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            @auth
                                <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                                    @csrf
                                    @php $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists(); @endphp
                                    <button class="flex items-center gap-2 text-sm font-medium {{ $isWishlisted ? 'text-rose-600' : 'text-gray-500' }} hover:text-rose-600 transition-colors">
                                        <svg class="w-5 h-5" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        {{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                    </button>
                                </form>
                            @endauth
                            <form method="POST" action="{{ route('compare.add', $product) }}">
                                @csrf
                                @php $isCompared = in_array($product->id, session('compare', [])); @endphp
                                <button class="flex items-center gap-2 text-sm font-medium {{ $isCompared ? 'text-emerald-600' : 'text-gray-500' }} hover:text-brand-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    {{ $isCompared ? 'Added to Compare' : 'Compare' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Seller Card -->
                    @if($product->seller)
                        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-card">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-50 to-brand-50 flex items-center justify-center text-lg font-bold text-brand-600">
                                        {{ strtoupper(substr($product->seller->company_name ?? $product->seller->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $product->seller->company_name ?? $product->seller->name }}</p>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= round($product->seller->rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                            <span class="text-xs text-gray-400 ml-1">({{ $product->seller->rating_count }})</span>
                                        </div>
                                    </div>
                                </div>
                                @if($product->seller->slug)
                                    <a href="{{ route('seller.show', $product->seller) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Visit Store</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- WhatsApp -->
                    <a href="https://wa.me/8801XXXXXXXXX?text=Hi%2C%20I'm%20interested%20in%20{{ urlencode($product->name) }}" target="_blank"
                       class="flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 hover:bg-emerald-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span class="text-sm font-semibold">Questions? Ask on WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <section class="mt-10" data-animate>
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-card">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                        <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>
                </section>
            @endif

            <!-- Reviews -->
            <section class="mt-10" data-animate>
                <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-card">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Reviews ({{ $product->reviews_count ?? 0 }})</h2>

                    @auth
                        <form method="POST" action="{{ route('reviews.store', $product) }}" class="mb-8 p-6 rounded-xl bg-gray-50 border border-gray-100">
                            @csrf
                            <h3 class="text-sm font-bold text-gray-900 mb-4">Write a Review</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex gap-1" x-data="{ rating: 0 }">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                                <svg class="w-8 h-8 transition-colors" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" x-model="rating">
                                    </div>
                                </div>
                                <div>
                                    <label for="review_title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input id="review_title" name="title" type="text" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm">
                                </div>
                                <div>
                                    <label for="review_comment" class="block text-sm font-medium text-gray-700">Comment</label>
                                    <textarea id="review_comment" name="comment" rows="3" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm"></textarea>
                                </div>
                                <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Submit Review</button>
                            </div>
                        </form>
                    @endauth

                    @if($product->approvedReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($product->approvedReviews as $review)
                                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-600">
                                                {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <h4 class="mt-2 text-sm font-semibold text-gray-900">{{ $review->title }}</h4>
                                    <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6 text-sm">No reviews yet. Be the first to review!</p>
                    @endif
                </div>
            </section>

            <!-- Related Products -->
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <section class="mt-12" data-animate>
                    <x-section-header title="Related Products" actionUrl="{{ route('shop.index', ['category' => $product->category_id]) }}" actionLabel="View All" />
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($relatedProducts as $related)
                            <x-product-card :product="$related" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Recommendations -->
            @auth
                <div class="mt-12">
                    <x-product-recommendations />
                </div>
            @endauth

            <div class="mt-8">
                <x-frequently-bought-together :product="$product" />
            </div>

            <div class="mt-8">
                <x-similar-products :product="$product" />
            </div>
        </main>

        <x-shop-footer />
    </div>

    <!-- Sticky Mobile Add to Cart -->
    <div x-show="true" x-data="{ atBottom: false }"
         x-init="window.addEventListener('scroll', () => { atBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 100; })"
         class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-100 bg-white/95 backdrop-blur p-4 lg:hidden shadow-elevated"
         :class="atBottom ? 'hidden' : ''">
        <div class="flex items-center gap-3 max-w-lg mx-auto">
            <div>
                <span class="text-lg font-black text-brand-600">{{ taka($product->current_price) }}</span>
                @if($product->is_on_sale)
                    <span class="text-sm text-gray-400 line-through ml-1">{{ taka($product->price) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" @disabled($product->stock <= 0)
                        class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 disabled:from-gray-300 disabled:to-gray-300 transition-all duration-200">
                    {{ $product->stock > 0 ? 'Add to Cart' : 'Sold Out' }}
                </button>
            </form>
        </div>
    </div>

    <x-facebook-sdk />
</body>
</html>

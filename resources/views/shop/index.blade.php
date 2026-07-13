<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo = \App\Services\SeoHelper::make();
        if (isset($selectedCategory) && $selectedCategory) {
            $seo->forCategory($selectedCategory);
        } else {
            $seo->forPage(
                __('Online Shopping in Bangladesh'),
                __('Shop electronics, fashion, books, and home essentials in Bangladesh with cash on delivery, fast shipping, and the best prices. Chokbazar — your marketplace.')
            );
        }
    @endphp

    <title>{{ $seo->getTitle() }}</title>
    {!! $seo->renderMeta() !!}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm+serif+display:400&family=dm+sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&family=noto+sans+bengali:100..900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f1ec] font-sans text-[#1a1a1a] antialiased">
    <div class="min-h-screen has-bottom-nav">
        <x-shop-header />

        <main>
            {{-- Hero --}}
            <x-hero-banner
                title="বাংলাদেশের সেরা অনলাইন শপ"
                description="ইলেকট্রনিক্স, ফ্যাশন, বই, হোম এসেনশিয়াল — প্রথম অর্ডারে ফ্রি ডেলিভারি, নগদে পেমেন্ট, সেরা দামের নিশ্চয়তা।"
                :searchRoute="route('shop.index')"
                :categories="$categories ?? []">
                <x-slot:title-en>Chokbazar — Bangladesh's Best Online Shop</x-slot:title-en>
            </x-hero-banner>

            {{-- Trust Badges — redesigned --}}
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4" data-animate>
                    <div class="rounded-lg bg-[#1a6b5e]/5 px-5 py-4 flex items-center gap-3 border border-[#1a6b5e]/10">
                        <span class="text-xl">📦</span>
                        <div>
                            <p class="text-sm font-semibold text-[#1a1a1a]">Cash on Delivery</p>
                            <p class="text-xs text-[#6b6b6b] mt-0.5">Pay when you receive</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-brand-600/5 px-5 py-4 flex items-center gap-3 border border-brand-600/10">
                        <span class="text-xl">📋</span>
                        <div>
                            <p class="text-sm font-semibold text-[#1a1a1a]">Live Stock Checks</p>
                            <p class="text-xs text-[#6b6b6b] mt-0.5">Real-time availability</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-[#1a6b5e]/5 px-5 py-4 flex items-center gap-3 border border-[#1a6b5e]/10">
                        <span class="text-xl">📍</span>
                        <div>
                            <p class="text-sm font-semibold text-[#1a1a1a]">Order Tracking</p>
                            <p class="text-xs text-[#6b6b6b] mt-0.5">Track in real-time</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-[#d4a853]/10 px-5 py-4 flex items-center gap-3 border border-[#d4a853]/20">
                        <span class="text-xl">💰</span>
                        <div>
                            <p class="text-sm font-semibold text-[#1a1a1a]">Best Price</p>
                            <p class="text-xs text-[#6b6b6b] mt-0.5">BDT pricing guaranteed</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nakshi divider --}}
            <x-nakshi-divider />

            {{-- Categories --}}
            @if(isset($categories) && $categories->count() > 0)
                <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
                    <div class="text-center mb-10">
                        <p class="font-bengali text-xs font-medium uppercase tracking-[0.2em] text-brand-600">ক্যাটাগরি</p>
                        <h2 class="font-display mt-2 text-3xl sm:text-4xl text-[#1a1a1a] leading-tight">Shop by Category</h2>
                        <p class="mt-2 text-sm text-[#6b6b6b]">Find what you need, fast</p>
                    </div>
                    <div class="flex gap-4 overflow-x-auto snap-x-mobile pb-2 sm:grid sm:grid-cols-3 lg:grid-cols-6 sm:overflow-x-visible sm:pb-0">
                        @foreach($categories->take(6) as $category)
                            <div class="shrink-0 w-[45vw] max-w-[200px] snap-start sm:w-auto sm:max-w-none">
                                <x-category-tile :category="$category" :count="$category->products_count ?? null" />
                            </div>
                        @endforeach
                    </div>
                    @if($categories->count() > 6)
                        <div class="text-center mt-8">
                            <a href="{{ route('categories') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                                View all categories <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Nakshi divider --}}
            <x-nakshi-divider />

            {{-- Featured Products --}}
            @if(isset($products) && $products->count() > 0)
                <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <p class="font-bengali text-xs font-medium uppercase tracking-[0.2em] text-brand-600">পণ্য</p>
                            <h2 class="font-display mt-1 text-3xl sm:text-4xl text-[#1a1a1a] leading-tight">
                                {{ ($sourcingMode ?? 'local') === 'import' ? 'Import Products' : 'Featured Products' }}
                            </h2>
                            <p class="mt-1 text-sm text-[#6b6b6b]">{{ $products->total() }} products available</p>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <x-sourcing-toggle :mode="$sourcingMode ?? 'local'" />

                        {{-- Desktop sort form --}}
                        <form method="GET" action="{{ route('shop.index') }}" class="hidden sm:flex gap-2">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            <select name="sort" class="rounded-lg border-gray-200 bg-white text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="name" @selected(request('sort', 'name') === 'name')>Name</option>
                                <option value="price" @selected(request('sort') === 'price')>Price</option>
                                <option value="rating" @selected(request('sort') === 'rating')>Rating</option>
                                <option value="popularity" @selected(request('sort') === 'popularity')>Popularity</option>
                            </select>
                            <select name="direction" class="rounded-lg border-gray-200 bg-white text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="asc" @selected(request('direction', 'asc') === 'asc')>Low to High</option>
                                <option value="desc" @selected(request('direction') === 'desc')>High to Low</option>
                            </select>
                            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Apply</button>
                        </form>

                        {{-- Mobile filter button --}}
                        <button x-data x-on:click="$dispatch('open-filter')" class="sm:hidden inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter & Sort
                        </button>
                    </div>

                    <div id="product-grid" class="mt-8">
                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-animate>
                                @foreach($products as $product)
                                    <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistedIds ?? [])" />
                                @endforeach
                            </div>
                            <div class="mt-8">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @else
                            <x-empty-state title="No products found" description="Try a different search or category." :actionUrl="route('shop.index')" actionLabel="Reset filters" />
                        @endif
                    </div>
                </section>
            @endif

            {{-- Nakshi divider --}}
            <x-nakshi-divider />

            {{-- CTA Section --}}
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-10" data-animate>
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a6b5e] to-[#0e4a40] p-8 sm:p-12 text-center text-white">
                    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 75% 25%, white 1px, transparent 1px); background-size: 24px 24px;" aria-hidden="true"></div>
                    <div class="relative">
                        <p class="font-bengali text-sm font-medium uppercase tracking-[0.2em] text-[#d4a853]/80">বিক্রেতা হোন</p>
                        <h2 class="font-display mt-3 text-2xl sm:text-3xl leading-tight">Start Selling on Chokbazar</h2>
                        <p class="mt-3 max-w-lg mx-auto text-white/70 text-sm sm:text-base">Join hundreds of sellers across Bangladesh. Reach thousands of customers every day.</p>
                        <a href="{{ route('seller.register') }}" class="mt-6 inline-flex items-center rounded-xl bg-[#d4a853] px-6 py-3 text-sm font-bold text-[#1a1a1a] hover:bg-[#c49a3e] transition-colors shadow-lg">
                            Become a Seller &rarr;
                        </a>
                    </div>
                </div>
            </section>
        </main>

        {{-- Mobile Filter/Sort Bottom Sheet --}}
        <div x-data="{ filterOpen: false }"
             @open-filter.window="filterOpen = true"
             x-show="filterOpen"
             class="fixed inset-0 z-50 sm:hidden" x-cloak>
            <div class="fixed inset-0 bg-black/30" @click="filterOpen = false"></div>
            <div class="fixed bottom-0 left-0 right-0 z-10 bg-white rounded-t-2xl shadow-elevated max-h-[70vh] overflow-y-auto"
                 x-show="filterOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full">
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Filter & Sort</h3>
                    <button @click="filterOpen = false" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="GET" action="{{ route('shop.index') }}" class="p-4 space-y-5">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort by</label>
                        <select name="sort" class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-brand-500 focus:ring-brand-500 py-3">
                            <option value="name" @selected(request('sort', 'name') === 'name')>Name</option>
                            <option value="price" @selected(request('sort') === 'price')>Price</option>
                            <option value="rating" @selected(request('sort') === 'rating')>Rating</option>
                            <option value="popularity" @selected(request('sort') === 'popularity')>Popularity</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Direction</label>
                        <div class="grid grid-cols-2 gap-3" x-data="{ dir: '{{ request('direction', 'asc') }}' }">
                            <label class="flex items-center justify-center gap-2 rounded-xl border-2 py-3 text-sm font-medium cursor-pointer transition-all duration-200"
                                   :class="dir === 'asc' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                <input type="radio" name="direction" value="asc" @checked(request('direction', 'asc') === 'asc') class="sr-only" @click="dir = 'asc'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                                Low to High
                            </label>
                            <label class="flex items-center justify-center gap-2 rounded-xl border-2 py-3 text-sm font-medium cursor-pointer transition-all duration-200"
                                   :class="dir === 'desc' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                <input type="radio" name="direction" value="desc" @checked(request('direction') === 'desc') class="sr-only" @click="dir = 'desc'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m4-4l4 4m-4-4l-4 4m4-4v12"/></svg>
                                High to Low
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 py-3.5 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 shadow-card">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <x-shop-footer />
    </div>
    <x-facebook-sdk />
</body>
</html>

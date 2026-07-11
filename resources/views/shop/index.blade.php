<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo = \App\Services\SeoHelper::make();
        if (isset($selectedCategory) && $selectedCategory) {
            $seo->forCategory($selectedCategory);
        } else {
            $seo->forPage(
                __('Online Shopping in Bangladesh'),
                __('Shop the best electronics, fashion, and home essentials in Bangladesh. Fast delivery, cash on delivery, and the best prices.')
            );
        }
    @endphp

    <title>{{ $seo->getTitle() }}</title>
    {!! $seo->renderMeta() !!}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="min-h-screen">
        <x-shop-header />

        <main>
            <!-- Hero Banner -->
            <x-hero-banner
                title="Shop the Best of Bangladesh"
                description="Electronics, fashion, books, home essentials — all at the best prices with fast delivery across the country."
                :searchRoute="route('shop.index')"
                :categories="$categories ?? collect([])">
            </x-hero-banner>

            <!-- Trust Badges -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-animate>
                    <div class="rounded-card border border-gray-100 bg-white p-5 shadow-card flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4a1 1 0 100-2 1 1 0 000 2zm0 0c-.552 0-1.052-.224-1.414-.586M15 9V5a2 2 0 012-2h4a2 2 0 012 2v5a2 2 0 01-2 2h-1"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Cash on Delivery</p>
                            <p class="text-xs text-gray-500 mt-0.5">Pay when you receive</p>
                        </div>
                    </div>
                    <div class="rounded-card border border-gray-100 bg-white p-5 shadow-card flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-brand-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Live Stock Checks</p>
                            <p class="text-xs text-gray-500 mt-0.5">Real-time availability</p>
                        </div>
                    </div>
                    <div class="rounded-card border border-gray-100 bg-white p-5 shadow-card flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Order Tracking</p>
                            <p class="text-xs text-gray-500 mt-0.5">Track in real-time</p>
                        </div>
                    </div>
                    <div class="rounded-card border border-gray-100 bg-white p-5 shadow-card flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 019.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Best Price</p>
                            <p class="text-xs text-gray-500 mt-0.5">BDT pricing guaranteed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            @if(isset($categories) && $categories->count() > 0)
                <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-10" data-animate>
                    <x-section-header title="Shop by Category" actionUrl="{{ route('categories') }}" actionLabel="View All" />
                    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        @foreach($categories->take(6) as $category)
                            <x-category-tile :category="$category" :count="$category->products_count ?? null" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Featured Products -->
            @if(isset($products) && $products->count() > 0)
                <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-12">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold text-gray-900">Featured Products</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ $products->total() }} products available</p>
                        </div>
                        <form method="GET" action="{{ route('shop.index') }}" class="flex gap-2">
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
                    </div>

                    <div id="product-grid" class="mt-6">
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

            <!-- CTA Section -->
            <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-16 mb-10" data-animate>
                <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 sm:p-12 text-center text-white">
                    <h2 class="text-2xl sm:text-3xl font-extrabold">Start Selling on {{ config('app.name') }}</h2>
                    <p class="mt-3 max-w-lg mx-auto text-brand-100 text-sm sm:text-base">Join hundreds of sellers across Bangladesh. Reach thousands of customers every day.</p>
                    <a href="{{ route('seller.register') }}" class="mt-6 inline-flex items-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-brand-700 hover:bg-brand-50 transition-colors shadow-elevated">
                        Become a Seller &rarr;
                    </a>
                </div>
            </section>
        </main>

        <x-shop-footer />
    </div>
    <x-facebook-sdk />
</body>
</html>

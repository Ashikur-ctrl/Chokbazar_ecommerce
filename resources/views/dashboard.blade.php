<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading font-bold text-2xl md:text-3xl text-[#1a1a1a] tracking-tight">
                    Welcome back, {{ $user->name }} 👋
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage your account, track orders, and explore your favorite items on Chokbazar.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-[#8f3c1f] text-white text-sm font-semibold shadow-md hover:bg-[#78321a] hover:shadow-lg transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                    Start Shopping
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#f6f1ec] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Admin / Seller Portal Alert Banners -->
            @if($user->isAdmin() && !empty($adminStats))
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#1a1a1a] via-[#2d221e] to-[#8f3c1f] text-white p-6 sm:p-8 shadow-xl border border-gold/20">
                    <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                        <svg class="w-72 h-72 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold uppercase tracking-wider mb-2">
                                👑 Administrator Control Suite
                            </div>
                            <h3 class="text-2xl font-bold font-heading text-white">Chokbazar Admin Operations</h3>
                            <p class="text-gray-300 text-sm mt-1 max-w-2xl">
                                Total Store Revenue: <strong class="text-emerald-400">৳{{ number_format($adminStats['total_revenue'], 2) }}</strong> &middot;
                                Pending Orders: <strong class="text-amber-400">{{ $adminStats['pending_orders'] }}</strong> &middot;
                                Products Listed: <strong class="text-blue-300">{{ $adminStats['total_products'] }}</strong>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="/admin" class="inline-flex items-center px-5 py-3 rounded-xl bg-amber-500 text-gray-900 font-bold text-sm hover:bg-amber-400 shadow-lg hover:shadow-amber-500/20 transition-all duration-200">
                                Filament Admin Panel
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="{{ route('admin-legacy.dashboard') }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-white/10 text-white font-medium text-sm border border-white/20 hover:bg-white/20 transition-all">
                                Legacy Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Metric Cards Overview Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Orders -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Orders</span>
                        <div class="w-10 h-10 rounded-xl bg-[#8f3c1f]/10 text-[#8f3c1f] flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 font-heading">{{ number_format($ordersCount) }}</span>
                        <p class="text-xs text-gray-500 mt-1">Orders placed to date</p>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Spend</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 font-heading">৳{{ number_format($totalSpent, 2) }}</span>
                        <p class="text-xs text-gray-500 mt-1">Successful purchases</p>
                    </div>
                </div>

                <!-- Saved Wishlist -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Wishlist</span>
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 font-heading">{{ number_format($wishlistCount) }}</span>
                        <p class="text-xs text-gray-500 mt-1">Saved items</p>
                    </div>
                </div>

                <!-- Saved Addresses -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Saved Addresses</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 font-heading">{{ number_format($addressesCount) }}</span>
                        <p class="text-xs text-gray-500 mt-1">Delivery locations</p>
                    </div>
                </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('shop.index') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-[#8f3c1f]/10 text-[#8f3c1f] flex items-center justify-center mb-2 group-hover:bg-[#8f3c1f] group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">Explore Catalog</span>
                </a>

                <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-2 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">My Orders</span>
                </a>

                <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center mb-2 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">Saved Wishlist</span>
                </a>

                <a href="{{ route('addresses.index') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-2 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">Addresses</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-2 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">Account Settings</span>
                </a>

                <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-[#8f3c1f]/40 hover:shadow-md transition-all text-center group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center mb-2 group-hover:bg-purple-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-900">View Cart</span>
                </a>
            </div>

            <!-- Recent Orders & Saved Items Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders List -->
                <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-heading">Recent Orders</h3>
                            <p class="text-xs text-gray-500">Track and manage your order fulfillments</p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="text-xs font-bold text-[#8f3c1f] hover:text-[#78321a] flex items-center">
                            View All Orders &rarr;
                        </a>
                    </div>

                    @if($recentOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors gap-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-gray-900">#{{ $order->order_number }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ match($order->status) { 'pending' => 'bg-amber-100 text-amber-800', 'delivered' => 'bg-emerald-100 text-emerald-800', 'shipped' => 'bg-blue-100 text-blue-800', 'cancelled' => 'bg-rose-100 text-rose-800', default => 'bg-gray-100 text-gray-800' } }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Placed on {{ $order->created_at->format('M d, Y') }} &middot; {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <span class="font-bold text-base text-gray-900">৳{{ number_format($order->total_amount, 2) }}</span>
                                        <a href="{{ route('orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                            Details &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900">No orders placed yet</h4>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-4">You haven't placed any orders on Chokbazar yet. Explore our curated products today!</p>
                            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-[#8f3c1f] text-white text-xs font-bold shadow-md hover:bg-[#78321a] transition-all">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Wishlist Quick View Sidebar -->
                <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-heading">Wishlist Highlights</h3>
                            <p class="text-xs text-gray-500">Quick view saved products</p>
                        </div>
                        <a href="{{ route('wishlist.index') }}" class="text-xs font-bold text-[#8f3c1f] hover:text-[#78321a]">
                            View All &rarr;
                        </a>
                    </div>

                    @if($wishlistItems->count() > 0)
                        <div class="space-y-4">
                            @foreach($wishlistItems as $item)
                                @if($item->product)
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                                        <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('shop.product', $item->product) }}" class="text-xs font-bold text-gray-900 truncate hover:text-[#8f3c1f] block">
                                                {{ $item->product->name }}
                                            </a>
                                            <p class="text-xs font-semibold text-[#8f3c1f] mt-0.5">৳{{ number_format($item->product->current_price ?? $item->product->price, 2) }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <p class="text-xs font-medium text-gray-600">Your wishlist is empty</p>
                            <a href="{{ route('shop.index') }}" class="text-xs font-bold text-[#8f3c1f] hover:underline mt-1 inline-block">Discover items to save &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

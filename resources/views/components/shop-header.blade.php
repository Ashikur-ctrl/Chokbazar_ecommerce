<div x-data="{ promoOpen: true, menuOpen: false, categoryOpen: false, searchOpen: false }" class="relative">
    <!-- Promo Bar -->
    <div x-show="promoOpen" class="relative bg-gradient-to-r from-brand-600 via-secondary-600 to-brand-700 py-2 text-center text-xs font-semibold text-white sm:text-sm">
        <span>@yield('promo_text', 'আজকের ডিল চলছে — দ্রুত অর্ডার করুন, Cash on Delivery available')</span>
        <button @click="promoOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Main Header -->
    <header class="sticky top-0 z-40 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <a href="{{ route('shop.index') }}" class="flex items-center shrink-0">
                <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto sm:h-10">
            </a>

            <!-- Search (Desktop) -->
            <div class="hidden md:block flex-1 max-w-lg mx-6">
                <form method="GET" action="{{ route('shop.index') }}" class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search in {{ config('app.name') }}..."
                           class="w-full rounded-full border-gray-200 bg-gray-50 pl-10 pr-4 py-2 text-sm transition-all duration-200 focus:border-brand-500 focus:ring-brand-500 focus:bg-white focus:w-full">
                </form>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-brand-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span id="cart-count" class="absolute -top-0.5 -right-0.5 hidden h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">0</span>
                </a>

                <!-- Auth -->
                @auth
                    <div x-data="{ userMenu: false }" class="relative">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-brand-600 transition-colors">
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>
                        <div x-show="userMenu" @click.away="userMenu = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white py-2 shadow-elevated">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Orders</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Wishlist</a>
                            <a href="{{ route('recently-viewed') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Recently Viewed</a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-brand-600 transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Register</a>
                    </div>
                    <a href="{{ route('login') }}" class="sm:hidden p-2 text-gray-600 hover:text-brand-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                @endauth

                <!-- Mobile Menu Toggle -->
                <button @click="menuOpen = !menuOpen" class="md:hidden p-2 text-gray-600 hover:text-brand-600">
                    <svg x-show="!menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Desktop Nav + Mega Menu -->
        <nav class="hidden md:flex items-center gap-1 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-gray-50 py-2">
            <!-- Categories Dropdown (Mega Menu) -->
            <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Categories
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute left-0 top-full mt-1 w-[500px] rounded-xl border border-gray-100 bg-white p-5 shadow-elevated z-50">
                    <div class="grid grid-cols-2 gap-4">
                        @php $headerCategories = \App\Models\Category::active()->orderBy('name')->get(); @endphp
                        @foreach($headerCategories as $cat)
                            <a href="{{ route('shop.index', ['category' => $cat->id]) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-brand-50 transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-50 to-secondary-50 flex items-center justify-center text-sm font-bold text-brand-600 group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($cat->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-brand-600">{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('categories') }}" class="mt-3 block text-center text-sm font-semibold text-brand-600 hover:text-brand-700 py-2 rounded-lg bg-brand-50 hover:bg-brand-100 transition-colors">
                        View All Categories &rarr;
                    </a>
                </div>
            </div>

            <a href="{{ route('shop.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors {{ request()->routeIs('shop.index') ? 'text-brand-600 bg-brand-50' : '' }}">Shop</a>
            <a href="{{ route('deals') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">Deals</a>
            <a href="{{ route('about') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">About</a>
            <a href="{{ route('contact') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">Contact</a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="ml-auto px-4 py-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">Admin</a>
                @endif
                @if(auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">Seller</a>
                @endif
            @endauth

            <a href="{{ route('seller.register') }}" class="ml-auto px-4 py-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">Become a Seller</a>
        </nav>
    </header>

    <!-- Mobile Menu Drawer -->
    <div x-show="menuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed inset-0 z-50 md:hidden" x-cloak>
        <div class="fixed inset-0 bg-black/30" @click="menuOpen = false"></div>
        <div class="relative bg-white w-72 h-full max-w-[85vw] shadow-elevated overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <span class="font-bold text-gray-900">Menu</span>
                <button @click="menuOpen = false" class="p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-1">
                <!-- Mobile Search -->
                <form method="GET" action="{{ route('shop.index') }}" class="mb-4 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                           class="w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500">
                </form>
                <a href="{{ route('shop.index') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Shop</a>
                <a href="{{ route('deals') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Deals</a>
                <a href="{{ route('categories') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Categories</a>
                <a href="{{ route('about') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">About</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Contact</a>
                <hr class="my-2 border-gray-100">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Dashboard</a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Orders</a>
                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Wishlist</a>
                    <hr class="my-2 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="block w-full text-left px-4 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-brand-600 hover:bg-brand-50 transition-colors">Register</a>
                @endauth
            </div>
        </div>
    </div>
</div>

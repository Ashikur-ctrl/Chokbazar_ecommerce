<nav x-data="{ open: false }" class="bg-white border-b border-brand-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('shop.index') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('shop.*') ? 'border-brand-500 text-brand-600' : '' }}">
                        Shop
                    </a>
                    <a href="{{ route('deals') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('deals') ? 'border-brand-500 text-brand-600' : '' }}">
                        Deals
                    </a>
                    <a href="{{ route('categories') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('categories') ? 'border-brand-500 text-brand-600' : '' }}">
                        Categories
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('contact') ? 'border-brand-500 text-brand-600' : '' }}">
                        Support
                    </a>
                    <a href="{{ route('compare') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('compare') ? 'border-brand-500 text-brand-600' : '' }}">
                        Compare
                    </a>
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('cart.*') ? 'border-brand-500 text-brand-600' : '' }}">
                        Cart
                        @auth
                            <span id="cart-count" class="ml-1 bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                        @endauth
                    </a>
                    @auth
                        <a href="{{ route('wishlist.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('wishlist.*') ? 'border-brand-500 text-brand-600' : '' }}">
                            Wishlist
                        </a>
                        <a href="{{ route('recently-viewed') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('recently-viewed') ? 'border-brand-500 text-brand-600' : '' }}">
                            Recent
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-brand-600 hover:border-brand-300 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-brand-500 text-brand-600' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('orders.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                            Orders
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin-legacy.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('admin-legacy.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Auth Links -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-slate-700 hover:text-brand-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-2 px-4 rounded-md">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('shop.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('shop.*') ? 'border-indigo-400 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                Shop
            </a>
            <a href="{{ route('cart.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('cart.*') ? 'border-indigo-400 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                Cart
                @auth
                    <span id="cart-count-mobile" class="ml-1 bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                @endauth
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                    Dashboard
                </a>
                <a href="{{ route('orders.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('orders.*') ? 'border-indigo-400 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                    Orders
                </a>
                <a href="{{ route('wishlist.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('wishlist.*') ? 'border-brand-400 text-brand-700 bg-brand-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                    Wishlist
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin-legacy.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin-legacy.*') ? 'border-indigo-400 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} transition duration-150 ease-in-out">
                        Admin
                    </a>
                @endif
            @endauth
        </div>

        <!-- Responsive Auth Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 space-y-1">
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Register</a>
                </div>
            </div>
        @endauth
    </div>
</nav>

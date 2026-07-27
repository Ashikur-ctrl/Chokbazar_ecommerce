<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Order Confirmed - {{ config('app.name', 'E-Commerce') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm+serif+display:400&family=dm+sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function(){const d=localStorage.getItem('darkMode')==='true'||(!('darkMode'in localStorage)&&window.matchMedia('(prefers-color-scheme:dark)').matches);if(d)document.documentElement.classList.add('dark');})();
    </script>
</head>
<body class="font-sans antialiased bg-[#f6f1ec] text-[#1a1a1a]">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('shop.index') }}" class="text-xl font-bold text-[#1a1a1a] font-display tracking-tight">Chokbazar</a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-800 hover:border-brand-500 transition duration-150 ease-in-out {{ request()->routeIs('shop.*') ? 'border-brand-600 text-[#1a1a1a]' : '' }}">Shop</a>
                            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-800 hover:border-brand-500 transition duration-150 ease-in-out {{ request()->routeIs('cart.*') ? 'border-brand-600 text-[#1a1a1a]' : '' }}">Cart@auth<span id="cart-count" class="ml-1 bg-rose-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>@endauth</a>
                            @auth<a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-800 hover:border-brand-500 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-brand-600 text-[#1a1a1a]' : '' }}">Dashboard</a>@endauth
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        @auth
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-[#6b6b6b] bg-white hover:text-[#1a1a1a] focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault();this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link></form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <div class="space-x-4">
                                <a href="{{ route('login') }}" class="text-[#6b6b6b] hover:text-[#1a1a1a] transition-colors">Login</a>
                                <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 text-white font-bold py-2 px-4 text-sm hover:from-brand-700 hover:to-brand-800 transition-all duration-200">Register</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Step Progress -->
                <div class="mb-10">
                    <div class="flex items-center justify-between max-w-lg mx-auto">
                        <div class="step">
                            <div class="step-indicator completed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-medium text-brand-700 hidden sm:inline">Cart</span>
                        </div>
                        <div class="step-line active"></div>
                        <div class="step">
                            <div class="step-indicator completed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-medium text-brand-700 hidden sm:inline">Checkout</span>
                        </div>
                        <div class="step-line active"></div>
                        <div class="step">
                            <div class="step-indicator active">3</div>
                            <span class="text-sm font-medium text-brand-600 hidden sm:inline">Confirmed</span>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-card mb-6">
                    <div class="p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 mb-4">
                            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-[#1a1a1a] font-display mb-2">Order Confirmed!</h1>
                        <p class="text-[#6b6b6b]">Thank you for your order. We've received your order and will process it shortly.</p>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                    <div class="p-6 sm:p-8">
                        <div class="border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-lg font-bold text-[#1a1a1a]">Order Details</h2>
                            <p class="text-sm text-[#6b6b6b] mt-1">Order #{{ $order->order_number }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Order Information -->
                            <div>
                                <h3 class="text-sm font-semibold text-[#1a1a1a] mb-3">Order Information</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Order Number</dt>
                                        <dd class="text-sm font-semibold text-[#1a1a1a]">{{ $order->order_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Order Date</dt>
                                        <dd class="text-sm font-semibold text-[#1a1a1a]">{{ $order->created_at->format('M d, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Status</dt>
                                        <dd class="text-sm font-semibold">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Payment</dt>
                                        <dd class="text-sm font-semibold">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Customer Information -->
                            <div>
                                <h3 class="text-sm font-semibold text-[#1a1a1a] mb-3">Customer Information</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Name</dt>
                                        <dd class="text-sm font-semibold text-[#1a1a1a]">{{ $order->customer_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-[#6b6b6b]">Email</dt>
                                        <dd class="text-sm font-semibold text-[#1a1a1a]">{{ $order->customer_email }}</dd>
                                    </div>
                                    @if($order->customer_phone)
                                        <div>
                                            <dt class="text-sm text-[#6b6b6b]">Phone</dt>
                                            <dd class="text-sm font-semibold text-[#1a1a1a]">{{ $order->customer_phone }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-[#1a1a1a] mb-3">Shipping Address</h3>
                            <p class="text-sm text-[#6b6b6b] whitespace-pre-line">{{ $order->shipping_address }}</p>
                        </div>

                        <!-- Order Items -->
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-[#1a1a1a] mb-3">Order Items</h3>
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b6b6b] uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b6b6b] uppercase tracking-wider">Price</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b6b6b] uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-[#6b6b6b] uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            @if($item->product && $item->product->image)
                                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}"
                                                                     class="h-10 w-10 object-cover rounded-lg">
                                                            @else
                                                                <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                                    <span class="text-xs text-gray-400">No img</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-[#1a1a1a]">{{ $item->product_name }}</div>
                                                            @if($item->product_sku)
                                                                <div class="text-sm text-[#6b6b6b]">SKU: {{ $item->product_sku }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ taka($item->price) }}</td>
                                                <td class="px-4 py-3 text-sm text-[#1a1a1a]">{{ $item->quantity }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-[#1a1a1a]">{{ taka($item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Totals -->
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Subtotal</span>
                                        <span class="text-[#1a1a1a]">{{ taka($order->subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Shipping</span>
                                        <span class="text-[#1a1a1a]">{{ taka($order->shipping_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Tax</span>
                                        <span class="text-[#1a1a1a]">{{ taka($order->tax_amount) }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-[#6b6b6b]">Discount</span>
                                            <span class="text-emerald-600">-{{ taka($order->discount_amount) }}</span>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-100 pt-2 flex justify-between text-lg font-bold">
                                        <span class="text-[#1a1a1a]">Total</span>
                                        <span class="text-brand-600">{{ taka($order->total_amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-center gap-4">
                    <a href="{{ route('shop.index') }}" class="rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 active:scale-[0.98] shadow-card">
                        Continue Shopping
                    </a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="rounded-xl border border-gray-200 bg-white px-6 py-3 text-sm font-bold text-[#1a1a1a] hover:bg-gray-50 transition-colors">
                            View Orders
                        </a>
                    @endauth
                </div>
            </div>
        </main>
    </div>
</body>
</html>
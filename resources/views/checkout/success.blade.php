<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Order Confirmed - {{ config('app.name', 'E-Commerce') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('shop.index') }}" class="text-xl font-bold text-gray-800">{{ config('app.name', 'E-Commerce') }}</a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('shop.*') ? 'border-indigo-400 text-gray-900' : '' }}">Shop</a>
                            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('cart.*') ? 'border-indigo-400 text-gray-900' : '' }}">Cart@auth<span id="cart-count" class="ml-1 bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>@endauth</a>
                            @auth<a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-gray-900' : '' }}">Dashboard</a>@endauth
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        @auth
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
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
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Register</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
                        <p class="text-gray-600">Thank you for your order. We've received your order and will process it shortly.</p>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="border-b border-gray-200 pb-4 mb-6">
                            <h2 class="text-lg font-medium text-gray-900">Order Details</h2>
                            <p class="text-sm text-gray-600 mt-1">Order #{{ $order->order_number }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Order Information -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Order Information</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm text-gray-600">Order Number</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $order->order_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-600">Order Date</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-600">Status</dt>
                                        <dd class="text-sm font-medium">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-600">Payment</dt>
                                        <dd class="text-sm font-medium">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Customer Information -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Customer Information</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm text-gray-600">Name</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-600">Email</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $order->customer_email }}</dd>
                                    </div>
                                    @if($order->customer_phone)
                                        <div>
                                            <dt class="text-sm text-gray-600">Phone</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $order->customer_phone }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Shipping Address</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $order->shipping_address }}</p>
                        </div>

                        <!-- Order Items -->
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Order Items</h3>
                            <div class="border border-gray-200 rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            @if($item->product && $item->product->image)
                                                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}"
                                                                     class="h-10 w-10 object-cover rounded">
                                                            @else
                                                                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                                                    <span class="text-xs text-gray-500">No img</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                                            @if($item->product_sku)
                                                                <div class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ taka($item->price) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->quantity }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ taka($item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Totals -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="text-gray-900">{{ taka($order->subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="text-gray-900">{{ taka($order->shipping_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="text-gray-900">{{ taka($order->tax_amount) }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Discount</span>
                                            <span class="text-green-600">-{{ taka($order->discount_amount) }}</span>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-medium">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-gray-900">{{ taka($order->total_amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-center space-x-4">
                    <a href="{{ route('shop.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                        Continue Shopping
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            View Dashboard
                        </a>
                    @endauth
                </div>
            </div>
        </main>
    </div>
</body>
</html>
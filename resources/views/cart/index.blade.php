<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shopping Cart</h2>
    </x-slot>

    <div class="bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert variant="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            @if($cartSummary['items_count'] > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <x-section-header title="Cart Items ({{ $cartSummary['items_count'] }})" />

                        @foreach($cartSummary['items'] as $item)
                            <div class="rounded-card border {{ $item['available'] ? 'border-gray-100 bg-white' : 'border-red-200 bg-red-50' }} p-4 shadow-card flex items-center gap-4 transition-all duration-200 hover:shadow-card-hover">
                                <!-- Image -->
                                <div class="shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-gray-100">
                                    @if($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-secondary-50">
                                            <span class="text-lg font-black text-brand-200">{{ strtoupper(substr($item['name'], 0, 2)) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 truncate">{{ $item['name'] }}</h4>
                                    <p class="text-sm font-semibold text-brand-600 mt-0.5">{{ $item['price'] }} each</p>
                                    @if(!$item['available'])
                                        <x-badge variant="danger" size="xs" class="mt-1">Unavailable</x-badge>
                                    @endif
                                </div>

                                <!-- Quantity -->
                                <div class="flex items-center">
                                    <form method="POST" action="{{ route('cart.update') }}" id="qty-form-{{ $item['id'] }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white">
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                                    class="flex items-center justify-center w-9 h-9 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-l-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="w-10 text-center text-sm font-semibold text-gray-900">{{ $item['quantity'] }}</span>
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                                    class="flex items-center justify-center w-9 h-9 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-r-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right min-w-[80px]">
                                    <p class="font-bold text-gray-900">{{ $item['subtotal'] }}</p>
                                </div>

                                <!-- Remove -->
                                <form method="POST" action="{{ route('cart.remove') }}" onsubmit="return confirm('Remove this item?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                    <button class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-between pt-2">
                            <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Clear entire cart?')">
                                @csrf @method('DELETE')
                                <button class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Clear Cart</button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="rounded-card bg-gradient-to-br from-gray-900 to-gray-800 p-6 shadow-card sticky top-24">
                            <h3 class="text-lg font-extrabold text-white mb-6">Order Summary</h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-gray-300">
                                    <span>Items ({{ $cartSummary['items_count'] }})</span>
                                    <span>{{ taka($cartSummary['subtotal']) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Shipping</span>
                                    <span>{{ taka($cartSummary['shipping_amount']) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Tax</span>
                                    <span>{{ taka($cartSummary['tax_amount']) }}</span>
                                </div>
                                @if($cartSummary['discount_amount'] > 0)
                                    <div class="flex justify-between text-emerald-400">
                                        <span>Discount</span>
                                        <span>-{{ taka($cartSummary['discount_amount']) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Coupon -->
                            <div class="my-5 rounded-lg bg-white/10 p-3">
                                @if(session('coupon'))
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-white">Coupon: <span class="text-brand-300">{{ session('coupon.code') }}</span></span>
                                        <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                            @csrf @method('DELETE')
                                            <button class="text-xs font-bold text-brand-300 hover:text-white transition-colors">Remove</button>
                                        </form>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                                        @csrf
                                        <input name="code" placeholder="Coupon code" class="min-w-0 flex-1 rounded-lg border-0 text-sm text-gray-900 focus:ring-brand-500">
                                        <button class="rounded-lg bg-brand-600 px-3 py-2 text-sm font-bold text-white hover:bg-brand-700 transition-colors">Apply</button>
                                    </form>
                                @endif
                            </div>

                            <hr class="border-white/10">

                            <div class="flex justify-between text-white text-lg font-bold mt-5 mb-6">
                                <span>Total</span>
                                <span>{{ $cartSummary['formatted_total'] }}</span>
                            </div>

                            <a href="{{ route('checkout.index') }}" class="block w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-3.5 text-center text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 shadow-card">
                                Proceed to Checkout
                            </a>
                            <a href="{{ route('shop.index') }}" class="block w-full rounded-xl border border-white/20 px-5 py-3 text-center text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 mt-3 transition-colors">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <x-empty-state title="Your cart is empty" description="Add some products to get started!" :actionUrl="route('shop.index')" actionLabel="Browse Products" icon="cart" />
            @endif
        </div>
    </div>
</x-app-layout>

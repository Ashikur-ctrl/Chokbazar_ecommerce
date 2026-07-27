<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#1a1a1a] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('shop.index') }}" class="rounded-2xl border border-gray-100 shadow-card bg-white p-6 hover:shadow-lg transition-all">
                    <p class="text-sm text-[#6b6b6b]">Browse</p>
                    <p class="mt-2 font-semibold text-[#1a1a1a]">Shop products</p>
                </a>

                <a href="{{ route('cart.index') }}" class="rounded-2xl border border-gray-100 shadow-card bg-white p-6 hover:shadow-lg transition-all">
                    <p class="text-sm text-[#6b6b6b]">Checkout</p>
                    <p class="mt-2 font-semibold text-[#1a1a1a]">View cart</p>
                </a>

                <a href="{{ route('orders.index') }}" class="rounded-2xl border border-gray-100 shadow-card bg-white p-6 hover:shadow-lg transition-all">
                    <p class="text-sm text-[#6b6b6b]">History</p>
                    <p class="mt-2 font-semibold text-[#1a1a1a]">My orders</p>
                </a>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="mt-6 rounded-2xl border border-gray-100 shadow-card bg-white">
                    <div class="p-6">
                        <a href="{{ route('admin-legacy.dashboard') }}" class="font-medium text-brand-600 hover:text-brand-700">Open admin dashboard &rarr;</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

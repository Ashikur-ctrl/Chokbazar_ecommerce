<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('shop.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <p class="text-sm text-gray-500">Browse</p>
                    <p class="mt-2 font-semibold text-gray-900">Shop products</p>
                </a>

                <a href="{{ route('cart.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <p class="text-sm text-gray-500">Checkout</p>
                    <p class="mt-2 font-semibold text-gray-900">View cart</p>
                </a>

                <a href="{{ route('orders.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50">
                    <p class="text-sm text-gray-500">History</p>
                    <p class="mt-2 font-semibold text-gray-900">My orders</p>
                </a>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <a href="{{ route('admin.dashboard') }}" class="font-medium text-indigo-600 hover:text-indigo-800">Open admin dashboard</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

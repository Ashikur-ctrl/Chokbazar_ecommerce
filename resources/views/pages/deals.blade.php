<x-app-layout>
    @section('title', 'Deals - ' . config('app.name'))

    <div class="bg-gray-50">
        <!-- Hero -->
        <div class="bg-gradient-to-r from-brand-600 via-brand-700 to-secondary-700 py-16 px-4 text-center text-white">
            <h1 class="text-4xl font-extrabold sm:text-5xl">Hot Deals</h1>
            <p class="mt-3 text-brand-100 max-w-lg mx-auto">Limited time offers — grab them before they're gone!</p>
            <div class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white/20 px-6 py-3 backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-lg font-bold" x-data="{ time: '06:24:59' }" x-text="time">06:24:59</span>
                <span class="text-sm">remaining</span>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-animate>
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-card hover:shadow-card-hover transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Electronics Weekend</h3>
                    <p class="mt-2 text-sm text-gray-500">Up to 40% off on electronics every weekend. Phones, laptops, accessories and more.</p>
                    <a href="{{ route('shop.index', ['search' => 'electronics']) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700">Shop Electronics &rarr;</a>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-card hover:shadow-card-hover transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Fashion Friday</h3>
                    <p class="mt-2 text-sm text-gray-500">Fresh fashion drops every Friday. New arrivals from top Bangladeshi brands.</p>
                    <a href="{{ route('shop.index', ['search' => 'fashion']) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700">Shop Fashion &rarr;</a>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-card hover:shadow-card-hover transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Home Essentials</h3>
                    <p class="mt-2 text-sm text-gray-500">Everything you need for your home at unbeatable prices. Kitchen, decor, furniture.</p>
                    <a href="{{ route('shop.index', ['search' => 'home']) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-brand-700">Shop Home &rarr;</a>
                </div>
            </div>

            <!-- Coupons -->
            <div class="mt-12 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-800 p-8 sm:p-10 text-white" data-animate>
                <h2 class="text-2xl font-bold">Exclusive Coupons</h2>
                <p class="mt-2 text-gray-300">Use these codes at checkout for extra savings.</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-dashed border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <span class="text-lg font-extrabold text-brand-400">BD10</span>
                        <p class="text-sm text-gray-300 mt-1">10% off on your first order</p>
                    </div>
                    <div class="rounded-xl border border-dashed border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <span class="text-lg font-extrabold text-brand-400">DHAKA100</span>
                        <p class="text-sm text-gray-300 mt-1">Free delivery in Dhaka city</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

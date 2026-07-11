<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <!-- About -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">{{ config('app.name') }}</h3>
                <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                    Bangladesh's trusted online marketplace. Shop electronics, fashion, home essentials and more with fast delivery across the country.
                </p>
                <div class="mt-4 flex gap-3">
                    <a href="#" class="text-gray-400 hover:text-brand-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-brand-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-brand-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm-2 16h-2v-6h2v6zm-1-6.891c-.607 0-1.1-.496-1.1-1.109 0-.612.492-1.109 1.1-1.109s1.1.497 1.1 1.109c0 .613-.493 1.109-1.1 1.109zM16 16h-2v-3c0-1.06-.94-2-2-2s-2 .94-2 2v3H8v-6h2v.842c.563-.512 1.29-.842 2-.842 1.629 0 3 1.371 3 3v3z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Quick Links</h3>
                <ul class="mt-4 space-y-3">
                    <li><a href="{{ route('shop.index') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Shop</a></li>
                    <li><a href="{{ route('deals') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Deals</a></li>
                    <li><a href="{{ route('categories') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Categories</a></li>
                    <li><a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Customer Care -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Customer Care</h3>
                <ul class="mt-4 space-y-3">
                    <li><a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">My Orders</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Shipping Info</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Returns Policy</a></li>
                    <li><a href="{{ route('compare') }}" class="text-sm text-gray-500 hover:text-brand-600 transition-colors">Compare Products</a></li>
                    <li><a href="{{ route('seller.register') }}" class="text-sm text-brand-600 hover:text-brand-700 font-semibold transition-colors">Become a Seller</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Contact Us</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-500">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        +880 1XXX-XXXXXX
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        support@example.com
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>We accept:</span>
                <span class="font-semibold text-gray-500">bKash</span>
                <span class="font-semibold text-gray-500">Nagad</span>
                <span class="font-semibold text-gray-500">Rocket</span>
                <span class="font-semibold text-gray-500">COD</span>
            </div>
        </div>
    </div>
</footer>

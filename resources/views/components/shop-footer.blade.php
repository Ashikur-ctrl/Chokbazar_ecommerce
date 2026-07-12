@php
    $currentYear = date('Y');
@endphp

<footer class="bg-[#1a1a1a] text-gray-400 mt-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('shop.index') }}" class="inline-block text-xl font-bold text-white font-display tracking-tight">
                    Chokbazar
                </a>
                <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                    Bangladesh's trusted online marketplace. Electronics, fashion, books, and home essentials — delivered to your doorstep.
                </p>
                <div class="mt-5 flex gap-3">
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:bg-brand-600 hover:text-white transition-all" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:bg-brand-600 hover:text-white transition-all" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:bg-brand-600 hover:text-white transition-all" aria-label="YouTube">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Quick Links</h3>
                <ul class="mt-4 space-y-2.5">
                    <li><a href="{{ route('shop.index') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Shop</a></li>
                    <li><a href="{{ route('deals') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Deals</a></li>
                    <li><a href="{{ route('categories') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Categories</a></li>
                    <li><a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            {{-- Customer Care --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Customer Care</h3>
                <ul class="mt-4 space-y-2.5">
                    <li><a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-white transition-colors">My Orders</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Shipping Information</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Returns & Exchanges</a></li>
                    <li><a href="{{ route('compare') }}" class="text-sm text-gray-500 hover:text-white transition-colors">Compare Products</a></li>
                    <li><a href="{{ route('seller.register') }}" class="text-sm font-semibold text-[#d4a853] hover:text-[#c49a3e] transition-colors">Become a Seller</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Contact Us</h3>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-start gap-2.5 text-sm text-gray-500">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:support@chokbazar.com" class="hover:text-white transition-colors">support@chokbazar.com</a>
                    </li>
                    <li class="flex items-start gap-2.5 text-sm text-gray-500">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+880 1700-000000</span>
                    </li>
                    <li class="flex items-start gap-2.5 text-sm text-gray-500">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Dhaka, Bangladesh</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-gray-600">
                &copy; {{ $currentYear }} Chokbazar. All rights reserved.
            </p>
            <div class="flex items-center gap-3 text-xs text-gray-600">
                <span>We accept</span>
                <span class="inline-flex items-center gap-1.5 text-gray-500 font-medium">
                    <span class="px-2 py-0.5 rounded bg-gray-800 text-[11px]">bKash</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 text-[11px]">Nagad</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 text-[11px]">Rocket</span>
                    <span class="px-2 py-0.5 rounded bg-gray-800 text-[11px]">COD</span>
                </span>
            </div>
        </div>
    </div>
</footer>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>
        <meta name="description" content="@yield('description', config('app.name') . ' - ' . __('Bangladesh-ready ecommerce'))">
        @yield('meta')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Store",
            "name": "{{ config('app.name') }}",
            "url": "{{ url('/') }}",
            "description": "{{ __('Bangladesh-ready ecommerce store.') }}",
            "currenciesAccepted": "BDT",
            "priceRange": "৳৳"
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Cart Count Update Script -->
        @auth
        <script>
            function updateCartCount() {
                fetch('/cart/summary')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.items_count;
                        const cartCount = document.getElementById('cart-count');
                        const cartCountMobile = document.getElementById('cart-count-mobile');

                        if (count > 0) {
                            if (cartCount) {
                                cartCount.textContent = count;
                                cartCount.classList.remove('hidden');
                            }
                            if (cartCountMobile) {
                                cartCountMobile.textContent = count;
                                cartCountMobile.classList.remove('hidden');
                            }
                        } else {
                            if (cartCount) cartCount.classList.add('hidden');
                            if (cartCountMobile) cartCountMobile.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error updating cart count:', error));
            }

            // Update cart count on page load
            document.addEventListener('DOMContentLoaded', updateCartCount);

            // Update cart count after form submissions
            document.addEventListener('submit', function(e) {
                if (e.target.action && e.target.action.includes('/cart/')) {
                    setTimeout(updateCartCount, 500);
                }
            });
        </script>
        @endauth
    </body>
</html>

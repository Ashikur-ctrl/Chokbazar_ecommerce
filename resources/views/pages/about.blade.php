<x-app-layout>
    @section('title', 'About Us - ' . config('app.name'))
    @section('description', 'Learn about our mission to provide the best online shopping experience in Bangladesh.')

    <div class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Hero -->
            <div class="text-center mb-12" data-animate>
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">About {{ config('app.name') }}</h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">Bangladesh's trusted online marketplace connecting customers with the best products at the best prices.</p>
            </div>

            <!-- Stats -->
            <div class="grid gap-6 sm:grid-cols-3 mb-16" data-animate>
                <div class="rounded-card border border-gray-100 bg-white p-8 text-center shadow-card">
                    <p class="text-4xl font-extrabold text-brand-600">500+</p>
                    <p class="mt-2 text-sm font-medium text-gray-600">Products</p>
                </div>
                <div class="rounded-card border border-gray-100 bg-white p-8 text-center shadow-card">
                    <p class="text-4xl font-extrabold text-brand-600">50+</p>
                    <p class="mt-2 text-sm font-medium text-gray-600">Sellers</p>
                </div>
                <div class="rounded-card border border-gray-100 bg-white p-8 text-center shadow-card">
                    <p class="text-4xl font-extrabold text-brand-600">1000+</p>
                    <p class="mt-2 text-sm font-medium text-gray-600">Happy Customers</p>
                </div>
            </div>

            <!-- Story -->
            <div class="rounded-2xl bg-white border border-gray-100 p-8 sm:p-12 shadow-card" data-animate>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Story</h2>
                <div class="prose prose-gray max-w-none">
                    <p class="text-gray-600 leading-relaxed">{{ config('app.name') }} was founded with a simple mission: make online shopping in Bangladesh easy, reliable, and affordable. We connect trusted sellers with customers across the country, offering everything from electronics to fashion to home essentials.</p>
                    <p class="text-gray-600 leading-relaxed mt-4">With cash on delivery, live stock tracking, and dedicated customer support, we're building the future of ecommerce in Bangladesh.</p>
                </div>
            </div>

            <!-- Promise -->
            <div class="mt-10 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-800 p-8 sm:p-12 text-white" data-animate>
                <h2 class="text-2xl font-bold mb-6">Our Promise</h2>
                <div class="grid gap-6 sm:grid-cols-3">
                    <div>
                        <h3 class="font-bold text-brand-400">Quality Products</h3>
                        <p class="mt-2 text-sm text-gray-300">Every product is verified and backed by our quality promise.</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-brand-400">Fast Delivery</h3>
                        <p class="mt-2 text-sm text-gray-300">Delivery across Bangladesh within 3-7 business days.</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-brand-400">24/7 Support</h3>
                        <p class="mt-2 text-sm text-gray-300">Our team is always here to help with your orders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

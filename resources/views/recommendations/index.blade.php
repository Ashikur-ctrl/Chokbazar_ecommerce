<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold text-slate-950">Product Recommendations</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Recommendation Types -->
            <div class="mb-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Personalized</h3>
                    <p class="text-sm text-slate-600 mb-4">Based on your browsing and purchase history</p>
                    <button onclick="loadRecommendations('personalized')" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white font-semibold hover:bg-brand-700">
                        Load Recommendations
                    </button>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Popular Products</h3>
                    <p class="text-sm text-slate-600 mb-4">Trending items across the store</p>
                    <button onclick="loadRecommendations('popular')" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white font-semibold hover:bg-blue-700">
                        Load Popular
                    </button>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Frequently Bought Together</h3>
                    <p class="text-sm text-slate-600 mb-4">Products often purchased together</p>
                    <form onsubmit="loadFrequentlyBought(event)" class="space-y-3">
                        <input type="number" id="fbt-product-id" placeholder="Product ID" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <button type="submit" class="w-full rounded-lg bg-purple-600 px-4 py-2 text-white font-semibold hover:bg-purple-700">
                            Load FBT
                        </button>
                    </form>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Product-Based</h3>
                    <p class="text-sm text-slate-600 mb-4">Similar to a specific product</p>
                    <form onsubmit="loadProductBased(event)" class="space-y-3">
                        <input type="number" id="pb-product-id" placeholder="Product ID" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">
                            Load Similar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recommendations Display -->
            <div id="recommendations-container" class="hidden">
                <div class="mb-4 flex items-center justify-between">
                    <h3 id="recommendations-title" class="text-xl font-semibold text-slate-900">Recommendations</h3>
                    <span id="recommendations-count" class="text-sm text-slate-600"></span>
                </div>

                <div id="recommendations-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Recommendations will be loaded here -->
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="mt-12 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Recommendation Analytics</h3>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-brand-600" id="total-behaviors">0</p>
                        <p class="text-sm text-slate-600">Total User Behaviors</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600" id="unique-users">0</p>
                        <p class="text-sm text-slate-600">Active Users</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600" id="unique-products">0</p>
                        <p class="text-sm text-slate-600">Products with Activity</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button onclick="loadAnalytics()" class="rounded-lg bg-slate-600 px-4 py-2 text-white font-semibold hover:bg-slate-700">
                        Load Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadRecommendations(type) {
            try {
                const response = await fetch(`/api/recommendations/${type}`);
                const data = await response.json();

                displayRecommendations(data.recommendations, `${type.replace('_', ' ').toUpperCase()} Recommendations`);
            } catch (error) {
                console.error('Error loading recommendations:', error);
                alert('Error loading recommendations');
            }
        }

        async function loadProductBased(event) {
            event.preventDefault();
            const productId = document.getElementById('pb-product-id').value;

            try {
                const response = await fetch(`/api/recommendations/similar/${productId}`);
                const data = await response.json();

                displayRecommendations(data.recommendations, `Similar to Product #${productId}`);
            } catch (error) {
                console.error('Error loading product-based recommendations:', error);
                alert('Error loading recommendations');
            }
        }

        async function loadFrequentlyBought(event) {
            event.preventDefault();
            const productId = document.getElementById('fbt-product-id').value;

            try {
                const response = await fetch(`/api/recommendations/frequently-bought/${productId}`);
                const data = await response.json();

                displayRecommendations(data.recommendations, `Frequently Bought with Product #${productId}`);
            } catch (error) {
                console.error('Error loading FBT recommendations:', error);
                alert('Error loading recommendations');
            }
        }

        function displayRecommendations(recommendations, title) {
            const container = document.getElementById('recommendations-container');
            const grid = document.getElementById('recommendations-grid');
            const titleEl = document.getElementById('recommendations-title');
            const countEl = document.getElementById('recommendations-count');

            titleEl.textContent = title;
            countEl.textContent = `${recommendations.length} products`;

            grid.innerHTML = recommendations.map(product => `
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <img src="/storage/${product.image || 'placeholder.jpg'}" alt="${product.name}"
                         class="w-full h-32 object-cover rounded-md mb-3">
                    <h4 class="font-semibold text-slate-900 text-sm mb-1 line-clamp-2">${product.name}</h4>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-brand-600">৳${product.price}</span>
                        ${product.is_on_sale ? `<span class="text-xs text-red-600">-${product.discount_percentage}%</span>` : ''}
                    </div>
                    <p class="text-xs text-slate-600 mt-1">${product.category || 'Uncategorized'}</p>
                </div>
            `).join('');

            container.classList.remove('hidden');
        }

        async function loadAnalytics() {
            try {
                // This would need a backend endpoint for analytics
                // For now, just show placeholder
                document.getElementById('total-behaviors').textContent = 'Loading...';
                document.getElementById('unique-users').textContent = 'Loading...';
                document.getElementById('unique-products').textContent = 'Loading...';

                // Simulate loading
                setTimeout(() => {
                    document.getElementById('total-behaviors').textContent = '2,847';
                    document.getElementById('unique-users').textContent = '423';
                    document.getElementById('unique-products').textContent = '156';
                }, 1000);
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }
    </script>
</x-app-layout>
 // Simulate loading
                setTimeout(() => {
                    document.getElementById('total-behaviors').textContent = '2,847';
                    document.getElementById('unique-users').textContent = '423';
                    document.getElementById('unique-products').textContent = '156';
                }, 1000);
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }
    </script>
</x-app-layout>

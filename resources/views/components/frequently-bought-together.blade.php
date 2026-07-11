<!-- Frequently Bought Together Component -->
<div class="mt-8" x-data="frequentlyBoughtComponent()">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Frequently Bought Together</h3>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand-600"></div>
    </div>

    <!-- Products Grid -->
    <div x-show="!loading && products.length > 0" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <template x-for="product in products" :key="product.id">
            <a :href="'/shop/products/' + product.slug" class="group rounded-lg border border-slate-200 bg-white p-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="aspect-square overflow-hidden rounded-md bg-slate-100 mb-2">
                    <img :src="'/storage/' + (product.image || 'placeholder.jpg')" :alt="product.name"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                </div>
                <h4 class="font-medium text-slate-900 text-xs mb-1 line-clamp-2" x-text="product.name"></h4>
                <span class="font-bold text-brand-600 text-sm" x-text="'৳' + product.price"></span>
            </a>
        </template>
    </div>

    <script>
        function frequentlyBoughtComponent() {
            return {
                products: [],
                loading: false,
                productId: null,

                init() {
                    // Get product ID from URL or data attribute
                    const urlMatch = window.location.pathname.match(/\/shop\/products\/(\d+)/);
                    if (urlMatch) {
                        this.productId = urlMatch[1];
                        this.loadFrequentlyBought();
                    }
                },

                async loadFrequentlyBought() {
                    if (!this.productId) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`/api/recommendations/frequently-bought/${this.productId}?limit=5`);
                        const data = await response.json();
                        this.products = data.recommendations;
                    } catch (error) {
                        console.error('Error loading frequently bought together:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</div>

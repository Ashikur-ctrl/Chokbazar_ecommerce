<!-- Similar Products Component -->
<div class="mt-8" x-data="similarProductsComponent()">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Customers Also Viewed</h3>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-600"></div>
    </div>

    <!-- Products Grid -->
    <div x-show="!loading && products.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <template x-for="product in products" :key="product.id">
            <a :href="'/shop/products/' + product.slug" class="group rounded-lg border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="aspect-square overflow-hidden rounded-md bg-slate-100 mb-3">
                    <img :src="'/storage/' + (product.image || 'placeholder.jpg')" :alt="product.name"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                </div>
                <h4 class="font-semibold text-slate-900 text-sm mb-1 line-clamp-2" x-text="product.name"></h4>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-orange-600" x-text="'৳' + product.price"></span>
                    <template x-if="product.is_on_sale">
                        <span class="text-xs text-red-600" x-text="'-' + product.discount_percentage + '%'"></span>
                    </template>
                </div>
                <p class="text-xs text-slate-600" x-text="product.category || 'Uncategorized'"></p>
            </a>
        </template>
    </div>

    <script>
        function similarProductsComponent() {
            return {
                products: [],
                loading: false,
                productId: null,

                init() {
                    // Get product ID from URL or data attribute
                    const urlMatch = window.location.pathname.match(/\/shop\/products\/(\d+)/);
                    if (urlMatch) {
                        this.productId = urlMatch[1];
                        this.loadSimilarProducts();
                    }
                },

                async loadSimilarProducts() {
                    if (!this.productId) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`/api/recommendations/similar/${this.productId}?limit=8`);
                        const data = await response.json();
                        this.products = data.recommendations;
                    } catch (error) {
                        console.error('Error loading similar products:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</div>

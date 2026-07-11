<!-- Product Recommendations Component -->
<div class="mt-8" x-data="recommendationsComponent()">
    <h3 class="text-lg font-semibold text-slate-900 mb-4" x-text="title"></h3>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
        <span class="ml-2 text-slate-600">Loading recommendations...</span>
    </div>

    <!-- Recommendations Grid -->
    <div x-show="!loading && recommendations.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <template x-for="product in recommendations" :key="product.id">
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

    <!-- Empty State -->
    <div x-show="!loading && recommendations.length === 0" class="text-center py-8 text-slate-600">
        <p>No recommendations available at this time.</p>
    </div>

    <script>
        function recommendationsComponent() {
            return {
                recommendations: [],
                loading: false,
                title: 'Recommended for You',

                init() {
                    this.loadRecommendations();
                },

                async loadRecommendations() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/recommendations?limit=8');
                        const data = await response.json();
                        this.recommendations = data.recommendations;
                        this.title = 'Recommended for You';
                    } catch (error) {
                        console.error('Error loading recommendations:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</div>

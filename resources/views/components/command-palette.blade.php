<div x-data="commandPalette()" x-init="init()"
     x-show="open"
     x-cloak
     @keydown.cmd.k.prevent="toggle()"
     @keydown.ctrl.k.prevent="toggle()"
     @keydown.escape.prevent="close()"
     @keydown="navigate($event)"
     class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh]"
     x-transition:enter="transition duration-200 ease-out"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150 ease-in"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="close()"></div>

    <!-- Panel -->
    <div class="relative w-full max-w-xl mx-4 glass-panel rounded-2xl shadow-glass overflow-hidden"
         @click.away="close()"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        <!-- Search Input -->
        <div class="flex items-center px-5 py-4 border-b border-border-subtle">
            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input x-ref="searchInput"
                   type="text"
                   x-model="query"
                   @input="search()"
                   placeholder="Search products, categories, orders..."
                   class="flex-1 ml-3 bg-transparent border-none outline-none text-base placeholder-gray-400 focus:ring-0">
            <kbd class="hidden sm:inline-flex items-center px-2 py-1 text-xs font-mono text-gray-400 bg-surface-container-low rounded border border-border-subtle">ESC</kbd>
        </div>

        <!-- Results -->
        <div class="max-h-96 overflow-y-auto">
            <!-- Empty state -->
            <template x-if="query.length > 0 && results.length === 0 && !loading">
                <div class="px-5 py-12 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">No results found for "<span x-text="query"></span>"</p>
                </div>
            </template>

            <!-- Loading -->
            <template x-if="loading">
                <div class="px-5 py-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="skeleton-shimmer w-10 h-10 rounded-lg"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="skeleton-shimmer h-3 w-3/4 rounded"></div>
                            <div class="skeleton-shimmer h-2 w-1/3 rounded"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="skeleton-shimmer w-10 h-10 rounded-lg"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="skeleton-shimmer h-3 w-2/3 rounded"></div>
                            <div class="skeleton-shimmer h-2 w-1/4 rounded"></div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Quick Links (default state) -->
            <template x-if="query.length === 0 && !loading">
                <div>
                    <!-- Browse -->
                    <div class="px-5 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Quick Links</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                <span class="material-symbols-outlined text-gray-400 text-lg">store</span>
                                Browse Shop
                            </a>
                            <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                <span class="material-symbols-outlined text-gray-400 text-lg">shopping_cart</span>
                                View Cart
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                <span class="material-symbols-outlined text-gray-400 text-lg">receipt_long</span>
                                My Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                <span class="material-symbols-outlined text-gray-400 text-lg">favorite</span>
                                Wishlist
                            </a>
                            @if(Route::has('compare.index'))
                            <a href="{{ route('compare.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                <span class="material-symbols-outlined text-gray-400 text-lg">compare_arrows</span>
                                Compare
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="px-5 py-3 border-t border-border-subtle">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Categories</p>
                        <div class="space-y-0.5 max-h-40 overflow-y-auto">
                            @if(isset($categories) && $categories->count())
                                @foreach($categories as $category)
                                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                       class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-surface-container-low transition-colors text-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            @else
                                <a href="{{ route('shop.index') }}" class="block px-3 py-2 text-sm text-gray-400">All Categories</a>
                            @endif
                        </div>
                    </div>

                    <!-- Shortcuts -->
                    <div class="px-5 py-3 border-t border-border-subtle">
                        <div class="flex flex-wrap gap-4 text-xs text-gray-400">
                            <span><kbd class="inline-flex items-center px-1.5 py-0.5 font-mono bg-surface-container-low rounded border border-border-subtle">↑↓</kbd> Navigate</span>
                            <span><kbd class="inline-flex items-center px-1.5 py-0.5 font-mono bg-surface-container-low rounded border border-border-subtle">↵</kbd> Open</span>
                            <span><kbd class="inline-flex items-center px-1.5 py-0.5 font-mono bg-surface-container-low rounded border border-border-subtle">ESC</kbd> Close</span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Search Results -->
            <template x-if="results.length > 0 && !loading">
                <div class="py-2">
                    <template x-for="(item, index) in results" :key="item.id">
                        <a :href="item.url"
                           class="flex items-center gap-4 px-5 py-3 hover:bg-surface-container-low transition-colors"
                           :class="{ 'bg-surface-container-low': selectedIndex === index }">
                            <!-- Icon/Image -->
                            <template x-if="item.image">
                                <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover border border-border-subtle">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-brand-500 text-lg" x-text="item.icon || 'inventory_2'"></span>
                                </div>
                            </template>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-400 truncate" x-text="item.subtitle || item.type"></p>
                            </div>
                            <!-- Price -->
                            <template x-if="item.price">
                                <span class="text-sm font-semibold text-brand-600 shrink-0" x-text="item.price"></span>
                            </template>
                            <!-- Badge -->
                            <template x-if="item.badge">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full shrink-0"
                                      :class="item.badge === 'Sale' ? 'bg-red-100 text-red-700' : 'bg-brand-100 text-brand-700'"
                                      x-text="item.badge"></span>
                            </template>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function commandPalette() {
        return {
            open: false,
            query: '',
            results: [],
            loading: false,
            selectedIndex: -1,
            debounceTimer: null,

            init() {
                // Listen for custom open event from header button
                document.addEventListener('open-command-palette', () => this.toggle());
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.query = '';
                    this.results = [];
                    this.selectedIndex = -1;
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                }
            },

            close() {
                this.open = false;
                this.query = '';
                this.results = [];
                this.selectedIndex = -1;
            },

            search() {
                if (this.debounceTimer) clearTimeout(this.debounceTimer);
                if (this.query.length < 2) {
                    this.results = [];
                    this.selectedIndex = -1;
                    return;
                }
                this.loading = true;
                this.selectedIndex = -1;
                this.debounceTimer = setTimeout(() => {
                    fetch(`/api/search?q=${encodeURIComponent(this.query)}&limit=8`)
                        .then(r => r.json())
                        .then(data => {
                            this.results = data.results || data.products || data.data || [];
                            this.loading = false;
                        })
                        .catch(() => {
                            // Fallback: search via shop index page
                            this.results = [];
                            this.loading = false;
                        });
                }, 250);
            },

            navigate(event) {
                if (!this.open) return;
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                } else if (event.key === 'Enter' && this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
                    event.preventDefault();
                    window.location.href = this.results[this.selectedIndex].url;
                }
            }
        };
    }
</script>

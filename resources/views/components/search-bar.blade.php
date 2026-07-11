@props(['route' => null, 'placeholder' => 'Search products...'])

<div x-data="{ open: false, query: '' }"
     {{ $attributes->merge(['class' => 'relative']) }}
     @click.away="open = false">
    <form method="GET" action="{{ $route ?? route('shop.index') }}" class="relative">
        <div class="relative flex items-center">
            <svg class="absolute left-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" x-model="query" @focus="open = true"
                   :class="open ? 'w-64 sm:w-80' : 'w-48 sm:w-56'"
                   class="w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2 text-sm transition-all duration-200 focus:border-brand-500 focus:ring-brand-500 focus:bg-white"
                   placeholder="{{ $placeholder }}">
        </div>
    </form>

    <!-- Quick suggestions dropdown -->
    <div x-show="open && query.length >= 2"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute top-full mt-1 left-0 right-0 bg-white rounded-lg border border-gray-200 shadow-elevated z-50 max-h-80 overflow-y-auto">
        <div class="p-2">
            <div x-show="loading" class="flex items-center gap-3 p-3">
                <div class="animate-spin h-5 w-5 border-2 border-brand-600 border-t-transparent rounded-full"></div>
                <span class="text-sm text-gray-500">Searching...</span>
            </div>
            <template x-for="result in suggestions" :key="result.id">
                <a :href="result.url" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-400" x-text="result.name.substring(0, 2).toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="result.name"></p>
                        <p class="text-xs text-brand-600 font-semibold" x-text="result.price"></p>
                    </div>
                </a>
            </template>
            <div x-show="!loading && suggestions.length === 0 && query.length >= 2" class="p-4 text-center text-sm text-gray-500">
                No products found for "<span x-text="query"></span>"
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchBar', () => ({
            query: '',
            open: false,
            loading: false,
            suggestions: [],
            async search() {
                if (this.query.length < 2) {
                    this.suggestions = [];
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch(`/search/suggestions?q=${encodeURIComponent(this.query)}`);
                    this.suggestions = await res.json();
                } catch (e) {
                    this.suggestions = [];
                }
                this.loading = false;
            }
        }));
    });
</script>
@endpush

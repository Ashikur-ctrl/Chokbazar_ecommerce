@props([
    'title' => '',
    'description' => '',
    'searchRoute' => '#',
    'categories' => collect([]),
    'compact' => false,
])

<section {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-b from-[#8f3c1f] to-[#6e2c15] text-white']) }}>
    {{-- Decorative pattern overlay --}}
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 25% 25%, white 1px, transparent 1px); background-size: 32px 32px;" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 {{ $compact ? 'py-12 sm:py-16' : 'py-16 sm:py-24 lg:py-32' }}">
        <div class="max-w-2xl">
            <p class="font-bengali text-sm font-medium uppercase tracking-[0.2em] text-amber-300/90">
                চোকবাজার
            </p>

            <h1 class="font-display mt-4 text-3xl sm:text-4xl lg:text-5xl leading-tight font-normal tracking-tight">
                {{ $title }}
            </h1>

            @if ($description)
                <p class="mt-4 text-base sm:text-lg text-white/80 max-w-xl leading-relaxed font-sans">
                    {{ $description }}
                </p>
            @endif

            @if (!$compact)
                <div class="mt-8 flex flex-col sm:flex-row gap-3 max-w-lg">
                    <form action="{{ $searchRoute }}" method="GET" class="flex-1 flex rounded-lg overflow-hidden ring-1 ring-white/20 focus-within:ring-2 focus-within:ring-amber-400 transition-all">
                        <input
                            type="text"
                            name="search"
                            placeholder="পণ্য খুঁজুন… (Search products)"
                            class="flex-1 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 focus:outline-none backdrop-blur-sm"
                            value="{{ request('search') }}"
                        />
                        <button type="submit" class="bg-amber-400/20 px-4 py-3 text-amber-300 hover:bg-amber-400/30 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    @if ($categories->isNotEmpty())
                        <select name="category" onchange="if(this.value) window.location=this.value" class="rounded-lg bg-white/10 px-4 py-3 text-sm text-white backdrop-blur-sm border border-white/20 focus:outline-none focus:ring-2 focus:ring-amber-400 appearance-none cursor-pointer">
                            <option value="" class="text-gray-800">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ route('shop.index', ['category' => $cat->id]) }}" class="text-gray-800">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-[#f6f1ec] to-transparent" aria-hidden="true"></div>
</section>

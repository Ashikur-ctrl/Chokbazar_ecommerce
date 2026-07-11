@props(['title', 'description' => null, 'searchRoute' => null, 'categories' => []])

<section {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-secondary-50']) }}>
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-100/30 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-secondary-100/30 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="max-w-2xl">
            <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-brand-600">Bangladesh Online Shopping</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight text-gray-900 sm:text-5xl lg:text-6xl">
                {{ $title }}
            </h1>
            @if($description)
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-gray-600">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if($searchRoute)
            <form method="GET" action="{{ $searchRoute }}" class="mt-8 flex flex-col gap-3 sm:flex-row max-w-2xl">
                <div class="flex-1 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                           class="w-full rounded-lg border-gray-200 bg-white/90 pl-10 pr-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 backdrop-blur-sm">
                </div>
                @if(count($categories) > 0)
                    <select name="category" class="rounded-lg border-gray-200 bg-white/90 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 backdrop-blur-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="rounded-lg bg-brand-600 px-6 py-3 text-sm font-bold text-white hover:bg-brand-700 transition-colors shadow-sm">
                    Search
                </button>
            </form>
        @endif

        {{ $slot ?? '' }}
    </div>
</section>

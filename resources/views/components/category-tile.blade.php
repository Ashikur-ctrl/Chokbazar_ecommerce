@props(['category', 'image' => null, 'count' => null])

<a href="{{ route('shop.index', ['category' => $category->id]) }}"
   {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-xl bg-white border border-gray-100/80 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5']) }}>
    <div class="aspect-[4/3] overflow-hidden bg-gradient-to-br from-brand-50 to-brand-50/50">
        @if($image)
            <img data-src="{{ $image }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center">
                <span class="text-6xl font-black text-brand-200/40">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
            </div>
        @endif
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-4">
        <h3 class="text-base font-semibold text-white drop-shadow-sm font-sans">{{ $category->name }}</h3>
        @if($count !== null)
            <p class="text-xs text-white/70 mt-0.5">{{ $count }} products</p>
        @endif
    </div>
</a>

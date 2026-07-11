@props(['src' => null, 'alt' => '', 'class' => ''])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gray-100 ' . $class]) }}
     x-data="{ loaded: false }">
    @if($src)
        <img src="{{ $src }}"
             alt="{{ $alt }}"
             loading="lazy"
             x-on:load="loaded = true"
             class="h-full w-full object-cover transition-opacity duration-300"
             :class="loaded ? 'opacity-100' : 'opacity-0'">
        <div x-show="!loaded"
             class="absolute inset-0 skeleton">
        </div>
    @else
        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-brand-50">
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif
    {{ $slot ?? '' }}
</div>

@props(['variant' => 'neutral', 'size' => 'sm'])

@php
    $classes = [
        'neutral' => 'bg-gray-100 text-gray-700',
        'success' => 'bg-emerald-100 text-emerald-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'danger' => 'bg-red-100 text-red-700',
        'info' => 'bg-blue-100 text-blue-700',
        'brand' => 'bg-brand-100 text-brand-700',
        'purple' => 'bg-purple-100 text-purple-700',
    ][$variant] ?? 'bg-gray-100 text-gray-700';

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-[10px]',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
    ][$size] ?? 'px-2.5 py-0.5 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-semibold {$sizes} {$classes}"]) }}>
    {{ $slot }}
</span>

@props(['type' => 'text', 'count' => 1, 'class' => ''])

@php
    $variants = [
        'text' => '<div class="skeleton-text"></div>',
        'text-sm' => '<div class="skeleton-text-sm"></div>',
        'avatar' => '<div class="skeleton-avatar"></div>',
        'card' => '<div class="skeleton-card"></div>',
        'stat' => '<div class="skeleton-stat"></div>',
        'thumb' => '<div class="skeleton-thumb"></div>',
        'product-card' => '
            <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
                <div class="skeleton-thumb"></div>
                <div class="p-4 space-y-3">
                    <div class="skeleton h-3 w-1/3"></div>
                    <div class="skeleton h-4 w-5/6"></div>
                    <div class="skeleton h-3 w-2/3"></div>
                    <div class="flex gap-2 pt-2">
                        <div class="skeleton h-4 w-20"></div>
                        <div class="skeleton h-4 w-16"></div>
                    </div>
                    <div class="skeleton h-10 w-full rounded-lg"></div>
                </div>
            </div>',
        'table-row' => '
            <div class="flex gap-4 p-4 border-b border-gray-100">
                <div class="skeleton h-10 w-10 rounded-lg shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="skeleton h-4 w-3/4"></div>
                    <div class="skeleton h-3 w-1/2"></div>
                </div>
                <div class="skeleton h-6 w-16 rounded-full shrink-0"></div>
            </div>',
        'product-grid' => '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">' . str_repeat('<div class="skeleton product-card-placeholder">
            <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
                <div class="skeleton-thumb"></div>
                <div class="p-4 space-y-3">
                    <div class="skeleton h-3 w-1/3"></div>
                    <div class="skeleton h-4 w-5/6"></div>
                    <div class="skeleton h-3 w-2/3"></div>
                    <div class="skeleton h-10 w-full rounded-lg"></div>
                </div>
            </div>
        </div>', max(1, $count)) . '</div>',
    ];
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    @for($i = 0; $i < $count; $i++)
        {!! $variants[$type] ?? $variants['text'] !!}
    @endfor
</div>

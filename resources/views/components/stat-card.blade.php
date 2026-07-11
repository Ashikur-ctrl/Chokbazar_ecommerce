@props(['label', 'value', 'icon' => null, 'trend' => null, 'trendUp' => true, 'color' => 'brand'])

@php
    $colors = [
        'brand' => 'text-brand-600',
        'secondary' => 'text-secondary-600',
        'emerald' => 'text-emerald-600',
        'red' => 'text-red-600',
        'amber' => 'text-amber-600',
        'purple' => 'text-purple-600',
    ];
    $valueColor = $colors[$color] ?? 'text-brand-600';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-card border border-gray-100 bg-white p-6 shadow-card hover:shadow-card-hover transition-shadow duration-200']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 flex items-center gap-1 text-sm {{ $trendUp ? 'text-emerald-600' : 'text-red-600' }}">
                    @if($trendUp)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    @endif
                    {{ $trend }}
                </p>
            @endif
        </div>
        @if($icon)
            <div class="shrink-0 ml-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    {!! $icon !!}
                </div>
            </div>
        @endif
    </div>
</div>

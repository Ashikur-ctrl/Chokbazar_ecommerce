@props(['title', 'description' => null, 'actionUrl' => null, 'actionLabel' => null, 'badge' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2']) }}>
    <div>
        <h2 class="text-2xl font-extrabold text-gray-900">{{ $title }}</h2>
        @if($description)
            <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="shrink-0 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors">
            {{ $actionLabel }} &rarr;
        </a>
    @endif
</div>

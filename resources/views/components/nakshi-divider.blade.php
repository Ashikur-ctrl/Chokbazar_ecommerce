@props(['color' => 'brand-200'])

<div class="flex items-center justify-center py-8 sm:py-12" aria-hidden="true">
    <svg width="160" height="16" viewBox="0 0 160 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-{{ $color }} opacity-60">
        {{-- Nakshi kantha-inspired woven stitch pattern --}}
        <path d="M0 8 H20 M20 4 L20 12 M20 8 H30 M30 4 L30 12 M30 8 H50 M50 4 L50 12 M50 8 H60 M60 2 Q65 0 70 2 Q75 4 80 2 Q85 0 90 2 Q95 4 100 2 Q105 0 110 2 L110 8 M110 8 H130 M130 4 L130 12 M130 8 H140 M140 4 L140 12 M140 8 H160"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
        <circle cx="20" cy="8" r="2" fill="currentColor" opacity="0.4"/>
        <circle cx="130" cy="8" r="2" fill="currentColor" opacity="0.4"/>
        <circle cx="80" cy="8" r="2.5" fill="currentColor" opacity="0.3"/>
        <circle cx="40" cy="8" r="1.5" fill="currentColor" opacity="0.25"/>
        <circle cx="120" cy="8" r="1.5" fill="currentColor" opacity="0.25"/>
    </svg>
</div>

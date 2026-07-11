@props(['title' => null, 'description' => null, 'image' => null, 'url' => null, 'type' => 'website', 'canonical' => null])

@php
    $seo = \App\Services\SeoHelper::make()
        ->title($title ?? config('app.name'))
        ->description($description ?? (config('app.name') . ' - ' . __('Bangladesh-ready ecommerce')))
        ->url($url ?? url()->current())
        ->type($type);

    if ($image) $seo->image($image);
    if ($canonical) $seo->canonical($canonical);
@endphp

<title>{{ $title ?? config('app.name') }}</title>
{!! $seo->renderMeta() !!}

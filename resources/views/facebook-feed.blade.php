<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
    <title>{{ config('app.name') }} - Facebook Feed</title>
    <link>{{ url('/') }}</link>
    <description>Product catalog for {{ config('app.name') }}</description>
    @foreach($products as $product)
    <item>
        <g:id>{{ $product->id }}</g:id>
        <g:title><![CDATA[{{ $product->seo_title ?? $product->name }}]]></g:title>
        <g:description><![CDATA[{{ $product->seo_description ?? ($product->short_description ?: strip_tags($product->description ?? '')) }}]]></g:description>
        <g:link>{{ route('shop.product', $product) }}</g:link>
        <g:image_link>{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}</g:image_link>
        <g:condition>new</g:condition>
        <g:availability>{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}</g:availability>
        <g:price>{{ number_format($product->current_price, 2) }} BDT</g:price>
        @if($product->sale_price)
        <g:sale_price>{{ number_format($product->sale_price, 2) }} BDT</g:sale_price>
        @endif
        <g:brand>{{ config('app.name') }}</g:brand>
        <g:gtin>{{ $product->sku ?: '' }}</g:gtin>
        <g:google_product_category>{{ $product->category->name ?? '' }}</g:google_product_category>
        @if($product->stock > 0)
        <g:inventory>{{ $product->stock }}</g:inventory>
        @endif
    </item>
    @endforeach
</channel>
</rss>

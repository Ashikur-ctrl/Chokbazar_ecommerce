<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('shop.index') }}</loc>
        <priority>0.9</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('deals') }}</loc>
        <priority>0.7</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('categories') }}</loc>
        <priority>0.7</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ route('about') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('contact') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ route('shop.index', ['category' => $category->id]) }}</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
    </url>
    @endforeach
    @foreach($products as $product)
    <url>
        <loc>{{ route('shop.product', $product) }}</loc>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
        <lastmod>{{ $product->updated_at->toW3cString() }}</lastmod>
    </url>
    @endforeach
</urlset>

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class SeoHelper
{
    private string $title = '';
    private string $description = '';
    private ?string $image = null;
    private string $url = '';
    private string $type = 'website';
    private ?string $canonical = null;
    private array $jsonLd = [];

    public static function make(): static
    {
        return new static;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function image(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function canonical(?string $url = null): static
    {
        $this->canonical = $url;
        return $this;
    }

    public function addJsonLd(array $data): static
    {
        $this->jsonLd[] = $data;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function forProduct(Product $product): static
    {
        $this->title = ($product->seo_title ?? $product->name) . ' - ' . config('app.name');
        $this->description = $product->seo_description ?? ($product->short_description ?: strip_tags($product->description ?? ''));
        $this->image = $product->image ? asset('storage/' . $product->image) : null;
        $this->url = route('shop.product', $product);
        $this->type = 'product';
        $this->canonical = route('shop.product', $product);

        $this->addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?: strip_tags($product->description ?? ''),
            'sku' => $product->sku,
            'image' => $this->image,
            'offers' => [
                '@type' => 'Offer',
                'price' => (float) ($product->sale_price ?? $product->price),
                'priceCurrency' => 'BDT',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => $this->url,
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => config('app.name'),
            ],
        ]);

        if ($product->relationLoaded('reviews') || $product->relationLoaded('approvedReviews')) {
            $avgRating = $product->average_rating;
            $count = $product->reviews_count;
            if ($count > 0) {
                $this->jsonLd[0]['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $avgRating,
                    'reviewCount' => $count,
                ];
            }
        }

        return $this;
    }

    public function forCategory(Category $category): static
    {
        $this->title = ($category->seo_title ?? $category->name) . ' - ' . config('app.name');
        $this->description = $category->seo_description ?? ($category->description ?? '');
        $this->url = route('shop.index', ['category' => $category->id]);
        $this->type = 'website';
        $this->canonical = route('shop.index', ['category' => $category->id]);

        $this->addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $category->description ?? '',
            'url' => $this->url,
        ]);

        return $this;
    }

    public function forPage(string $title, string $description, ?string $image = null): static
    {
        $this->title = $title . ' - ' . config('app.name');
        $this->description = $description;
        $this->image = $image;
        $this->type = 'website';
        return $this;
    }

    public function renderMeta(): string
    {
        $html = '';

        if ($this->canonical) {
            $html .= '<link rel="canonical" href="' . e($this->canonical) . '">' . "\n";
        }

        $html .= '<meta name="description" content="' . e($this->description) . '">' . "\n";
        $html .= '<meta property="og:title" content="' . e($this->title) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($this->description) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($this->type) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($this->url ?: url()->current()) . '">' . "\n";

        if ($this->image) {
            $html .= '<meta property="og:image" content="' . e($this->image) . '">' . "\n";
            $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        } else {
            $html .= '<meta name="twitter:card" content="summary">' . "\n";
        }

        $html .= '<meta name="twitter:title" content="' . e($this->title) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($this->description) . '">' . "\n";

        return $html;
    }

    public function renderJsonLd(): string
    {
        if (empty($this->jsonLd)) {
            return '';
        }

        $html = '';
        foreach ($this->jsonLd as $data) {
            $html .= '<script type="application/ld+json">' . "\n";
            $html .= json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $html .= "\n" . '</script>' . "\n";
        }

        return $html;
    }

    public function render(): string
    {
        $html = $this->renderMeta();
        if (!empty($this->jsonLd)) {
            $html .= $this->renderJsonLd();
        }
        return $html;
    }
}

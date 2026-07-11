<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate a static sitemap.xml file in the public directory';

    public function handle(): int
    {
        $base = config('app.url');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $xml .= '<url><loc>' . $base . '/</loc><priority>1.0</priority><changefreq>daily</changefreq></url>';
        $xml .= '<url><loc>' . $base . '/shop</loc><priority>0.9</priority><changefreq>daily</changefreq></url>';
        $xml .= '<url><loc>' . $base . '/deals</loc><priority>0.7</priority><changefreq>daily</changefreq></url>';
        $xml .= '<url><loc>' . $base . '/categories</loc><priority>0.7</priority><changefreq>weekly</changefreq></url>';
        $xml .= '<url><loc>' . $base . '/about</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>';
        $xml .= '<url><loc>' . $base . '/contact</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>';

        foreach (Category::active()->get() as $category) {
            $xml .= '<url><loc>' . $base . '/shop?category=' . $category->id . '</loc><priority>0.8</priority><changefreq>daily</changefreq></url>';
        }

        foreach (Product::active()->select('slug', 'updated_at')->cursor() as $product) {
            $xml .= '<url><loc>' . $base . '/shop/product/' . $product->slug . '</loc><priority>0.8</priority><changefreq>weekly</changefreq><lastmod>' . $product->updated_at->toW3cString() . '</lastmod></url>';
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap.xml'), $xml);

        $count = substr_count($xml, '<url><loc>');
        $this->info("Sitemap generated: {$count} URLs, " . strlen($xml) . ' bytes');

        return self::SUCCESS;
    }
}

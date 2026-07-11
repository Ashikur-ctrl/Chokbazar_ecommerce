<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::active()->select('slug', 'updated_at')->get();
        $categories = Category::active()->select('id', 'slug', 'updated_at')->get();

        return response()->view('sitemap', compact('products', 'categories'))->header('Content-Type', 'text/xml');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;

class FacebookFeedController extends Controller
{
    public function feed()
    {
        $products = Product::with(['category', 'images' => fn($q) => $q->where('is_primary', true)])
            ->active()
            ->get();

        return response()->view('facebook-feed', compact('products'))
            ->header('Content-Type', 'application/xml');
    }
}

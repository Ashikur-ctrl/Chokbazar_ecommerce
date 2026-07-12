<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->withCount(['orderItems', 'wishlists'])
            ->active()
            ->inStock();

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock')) {
            match ($request->stock) {
                'low' => $query->whereColumn('stock', '<=', 'low_stock_threshold'),
                'out' => $query->where('stock', '<=', 0),
                default => $query->where('stock', '>', 0),
            };
        }

        // Sort options
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'rating':
                $query->withAvg('approvedReviews as rating_average', 'rating')
                    ->orderBy('rating_average', $sortDirection);
                break;
            case 'popularity':
                $query->withCount('orderItems')
                    ->orderBy('order_items_count', $sortDirection);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }

        // Sourcing mode filter
        $sourcingMode = session('sourcing_mode', 'local');
        if (in_array($sourcingMode, ['local', 'import'])) {
            $query->sourcingType($sourcingMode);
        }

        $products = $query->paginate(12);
        $categories = cache()->remember('categories_active', 3600, function () {
            return Category::active()->withCount('products')->get();
        });
        $selectedCategory = null;

        if ($request->has('category') && $request->category) {
            $selectedCategory = Category::find($request->category);
        }

        // Determine wishlist state for current user for server-rendered grid
        $wishlistedIds = [];
        if (auth()->check()) {
            $wishlistedIds = \App\Models\Wishlist::where('user_id', auth()->id())
                ->whereIn('product_id', $products->pluck('id')->toArray())
                ->pluck('product_id')
                ->toArray();
        }

        return view('shop.index', compact('products', 'categories', 'selectedCategory', 'wishlistedIds', 'sourcingMode'));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Only show active products
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'images', 'approvedReviews.user']);

        // Get related products from same category
        $relatedProducts = Product::with(['category', 'images'])
            ->withCount(['orderItems', 'wishlists'])
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}

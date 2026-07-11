<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function filter(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->withCount(['orderItems', 'wishlists'])
            ->active()
            ->inStock();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('tags', 'like', "%{$term}%");
            });
        }

        if ($request->filled('stock')) {
            match ($request->stock) {
                'low' => $query->whereColumn('stock', '<=', 'low_stock_threshold'),
                'out' => $query->where('stock', '<=', 0),
                default => $query->where('stock', '>', 0),
            };
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        if ($request->filled('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $products = $query->paginate(12);

        // Optional rating filter applied after query to avoid complex SQL
        if ($request->filled('rating_min')) {
            $min = (float) $request->rating_min;
            $filtered = $products->filter(function ($product) use ($min) {
                return $product->average_rating >= $min;
            });

            $products = new LengthAwarePaginator(
                $filtered->values(),
                $filtered->count(),
                $products->perPage(),
                $products->currentPage(),
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // Determine wishlist state for current user for products on this page
        $userId = auth()->id();
        $wishlisted = [];
        if ($userId) {
            $wishlisted = \App\Models\Wishlist::where('user_id', $userId)
                ->whereIn('product_id', $products->pluck('id')->toArray())
                ->pluck('product_id')
                ->toArray();
        }

        // Determine compare state from session
        $compareIds = session()->get('compare', []);

        return response()->json([
            'products' => $products->through(function ($product) use ($wishlisted) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->current_price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->image,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                    'category' => $product->category?->name,
                    'stock' => $product->stock,
                    'labels' => $product->labels,
                    'average_rating' => $product->average_rating,
                    'order_items_count' => $product->order_items_count ?? 0,
                    'wishlists_count' => $product->wishlists_count ?? 0,
                    'is_in_wishlist' => in_array($product->id, $wishlisted),
                    'is_in_compare' => in_array($product->id, $compareIds),
                ];
            }),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }
}

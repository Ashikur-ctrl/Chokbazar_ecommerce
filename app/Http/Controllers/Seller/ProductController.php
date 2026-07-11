<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $sellerId = auth()->user()->seller_id;

        $query = Product::with('category')->where('seller_id', $sellerId);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->active(),
                'inactive' => $query->where('is_active', false),
                'low_stock' => $query->whereColumn('stock', '<=', 'low_stock_threshold'),
                default => null,
            };
        }

        $products = $query->latest()->paginate(15);

        return view('seller.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'moq' => 'nullable|integer|min:1',
            'is_wholesale' => 'nullable|boolean',
            'is_b2b_only' => 'nullable|boolean',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'tags' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_featured' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'short_description' => $validated['short_description'] ?? '',
            'price' => (float) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (float) $validated['sale_price'] : null,
            'cost_price' => !empty($validated['cost_price']) ? (float) $validated['cost_price'] : 0,
            'stock' => (int) $validated['stock'],
            'moq' => (int) ($validated['moq'] ?? 1),
            'is_wholesale' => $request->boolean('is_wholesale'),
            'is_b2b_only' => $request->boolean('is_b2b_only'),
            'sku' => $validated['sku'] ?? '',
            'tags' => $request->filled('tags') ? array_map('trim', explode(',', $validated['tags'])) : [],
            'category_id' => $validated['category_id'] ?? null,
            'seller_id' => auth()->user()->seller_id,
            'is_active' => true,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->hasFile('images')) {
            $this->handleImages($request, $product);
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $this->authorizeSeller($product);

        $categories = Category::active()->orderBy('name')->get();
        $product->load('images');

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeSeller($product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'moq' => 'nullable|integer|min:1',
            'is_wholesale' => 'nullable|boolean',
            'is_b2b_only' => 'nullable|boolean',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'tags' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'short_description' => $validated['short_description'] ?? '',
            'price' => (float) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (float) $validated['sale_price'] : null,
            'cost_price' => !empty($validated['cost_price']) ? (float) $validated['cost_price'] : 0,
            'stock' => (int) $validated['stock'],
            'moq' => (int) ($validated['moq'] ?? 1),
            'is_wholesale' => $request->boolean('is_wholesale'),
            'is_b2b_only' => $request->boolean('is_b2b_only'),
            'sku' => $validated['sku'] ?? '',
            'tags' => $request->filled('tags') ? array_map('trim', explode(',', $validated['tags'])) : [],
            'category_id' => $validated['category_id'] ?? null,
            'is_active' => $request->boolean('is_active', $product->is_active),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->hasFile('images')) {
            $this->handleImages($request, $product);
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorizeSeller($product);

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Product deleted.');
    }

    private function handleImages(Request $request, Product $product): void
    {
        $primarySet = $product->image !== null;

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'alt_text' => $product->name . ($index > 0 ? ' - View ' . ($index + 1) : ''),
                'is_primary' => !$primarySet && $index === 0,
                'sort_order' => $product->images()->count() + $index,
            ]);

            if (!$primarySet && $index === 0) {
                $product->update(['image' => $path]);
                $primarySet = true;
            }
        }
    }

    private function authorizeSeller(Product $product): void
    {
        if ($product->seller_id !== auth()->user()->seller_id) {
            abort(403, 'This product does not belong to you.');
        }
    }
}

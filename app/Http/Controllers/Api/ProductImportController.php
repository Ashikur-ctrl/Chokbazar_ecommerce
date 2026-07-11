<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'specs' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'tags' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Resolve category
        $categoryId = $request->integer('category_id');
        if (!$categoryId && $request->filled('category')) {
            $category = Category::firstOrCreate(
                ['name' => trim($request->category)],
                ['slug' => Str::slug(trim($request->category)), 'is_active' => true]
            );
            $categoryId = $category->id;
        }

        // Build description
        $description = $request->input('description', '');
        if ($request->filled('specs')) {
            $description .= ($description ? "\n\nSpecifications:\n" : "Specifications:\n") . $request->input('specs');
        }

        // Generate unique slug
        $slug = Str::slug($request->name);
        $baseSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $description,
            'short_description' => $request->input('short_description', ''),
            'price' => (float) $request->input('selling_price'),
            'cost_price' => (float) ($request->input('cost_price', 0)),
            'stock' => max(0, (int) $request->input('stock', 0)),
            'sku' => $request->input('sku', ''),
            'tags' => $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : [],
            'seo_title' => $request->input('seo_title', $request->name),
            'seo_description' => $request->input('seo_description', $request->input('short_description', $request->input('specs', $request->name))),
            'is_active' => true,
            'category_id' => $categoryId,
        ]);

        // Handle images
        $primarySet = false;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name . ($index > 0 ? ' - View ' . ($index + 1) : ''),
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);

                if (!$primarySet) {
                    $product->update(['image' => $path]);
                    $primarySet = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('shop.product', $product),
            ],
        ], 201);
    }
}

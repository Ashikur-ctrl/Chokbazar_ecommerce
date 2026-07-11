<x-seller-layout>
    <x-slot:title>Edit Product</x-slot:title>
    <x-slot:heading>Edit Product</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">{{ $product->name }}</p></x-slot:subheading>

    <form method="POST" action="{{ route('seller.products.update', $product) }}" enctype="multipart/form-data" class="max-w-3xl">
        @csrf @method('PATCH')

        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price (৳) *</label>
                    <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price (৳)</label>
                    <input id="sale_price" name="sale_price" type="number" step="0.01" value="{{ old('sale_price', $product->sale_price) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price (৳)</label>
                    <input id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity *</label>
                    <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="moq" class="block text-sm font-medium text-gray-700">Min. Order Quantity</label>
                    <input id="moq" name="moq" type="number" value="{{ old('moq', $product->moq) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="category_id" name="category_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                <input id="short_description" name="short_description" type="text" value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Full Description</label>
                <textarea id="description" name="description" rows="5"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700">Tags (comma separated)</label>
                <input id="tags" name="tags" type="text" value="{{ old('tags', is_array($product->tags) ? implode(',', $product->tags) : $product->tags) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div class="grid gap-6 sm:grid-cols-4">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_wholesale" value="1" @checked(old('is_wholesale', $product->is_wholesale))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Wholesale</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_b2b_only" value="1" @checked(old('is_b2b_only', $product->is_b2b_only))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">B2B Only</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Featured</span>
                </label>
            </div>

            @if($product->images->count() > 0)
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Current Images</p>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                            <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="w-full h-full object-cover">
                                @if($image->is_primary)
                                    <span class="absolute top-1 left-1 bg-brand-500 text-white text-xs px-1.5 py-0.5 rounded">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                <input id="images" name="images[]" type="file" multiple accept="image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Update Product</button>
                <a href="{{ route('seller.products.index') }}" class="text-sm text-gray-600 hover:text-gray-700">Cancel</a>
            </div>
        </div>
    </form>
</x-seller-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}
            </h2>
            <div>
                <a href="{{ route('products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Products
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Image -->
                        <div>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->name }}</h3>

                            <div class="mb-4">
                                <span class="text-3xl font-bold text-green-600">{{ taka($product->current_price) }}</span>
                                @if($product->is_on_sale)
                                    <span class="ml-2 text-lg text-gray-500 line-through">{{ taka($product->price) }}</span>
                                    <span class="ml-2 bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                        {{ $product->discount_percentage }}% OFF
                                    </span>
                                @endif
                            </div>

                            <div class="mb-6">
                                @if($product->is_in_stock)
                                    <form method="POST" action="{{ route('cart.add') }}" class="flex items-center space-x-4">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="flex items-center">
                                            <label for="quantity" class="mr-2 text-sm font-medium">Quantity:</label>
                                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                                   class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-2 rounded">
                                        Out of Stock
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2 text-sm">
                                <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                                <p><strong>Stock:</strong>
                                    <span class="{{ $product->is_in_stock ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $product->stock }} {{ $product->is_in_stock ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </p>
                                @if($product->sku)
                                    <p><strong>SKU:</strong> {{ $product->sku }}</p>
                                @endif
                                <p><strong>Status:</strong>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                @if($product->is_featured)
                                    <p><strong>Featured:</strong> <span class="text-yellow-600">⭐ Yes</span></p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Short Description -->
                    @if($product->short_description)
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Summary</h4>
                            <p class="text-gray-700">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    <!-- Full Description -->
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Description</h4>
                        <div class="text-gray-700 prose max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>

                    <!-- Additional Images -->
                    @if($product->images->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Additional Images</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($product->images as $image)
                                    <div class="relative">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? $product->name }}" class="w-full h-24 object-cover rounded">
                                        @if($image->is_primary)
                                            <span class="absolute top-1 right-1 bg-blue-500 text-white text-xs px-2 py-1 rounded">Primary</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
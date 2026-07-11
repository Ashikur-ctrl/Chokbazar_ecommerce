<x-app-layout>
    @section('title', 'Categories - ' . config('app.name'))

    <div class="bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" data-animate>
                <h1 class="text-4xl font-extrabold text-gray-900">Categories</h1>
                <p class="mt-3 text-gray-600">Browse products by category</p>
            </div>

            @php $allCategories = \App\Models\Category::active()->withCount('products')->get(); @endphp
            @if($allCategories->count() > 0)
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-animate>
                    @foreach($allCategories as $category)
                        <x-category-tile :category="$category" :count="$category->products_count" class="h-48" />
                    @endforeach
                </div>
            @else
                <x-empty-state title="No categories yet" description="Categories will appear here once products are added." />
            @endif
        </div>
    </div>
</x-app-layout>

<x-seller-layout>
    <x-slot:title>AI Product Upload</x-slot:title>
    <x-slot:heading>AI Product Upload</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Upload one product image and Chokbazar prepares the listing draft.</p></x-slot:subheading>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px]">
        <form method="POST" action="{{ route('seller.ai-products.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
            @csrf

            <div>
                <label for="image" class="block text-sm font-bold text-gray-900">Product image</label>
                <div class="mt-3 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-brand-700">
                    <p class="mt-3 text-xs text-gray-500">JPG, PNG, or WebP. Max 5 MB.</p>
                </div>
            </div>

            <div>
                <label for="note" class="block text-sm font-bold text-gray-900">Optional note</label>
                <textarea id="note" name="note" rows="3" maxlength="500" placeholder="Example: cotton, black, 3 sizes, cost around 250 taka" class="mt-2 block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="inline-flex items-center rounded-lg bg-brand-600 px-5 py-3 text-sm font-bold text-white hover:bg-brand-700">
                Generate Product Draft
            </button>
        </form>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-900">What happens next</h2>
            <div class="mt-5 space-y-4 text-sm text-gray-600">
                <div class="flex gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">1</span>
                    <p>AI reads the image and prepares the title, description, tags, and suggested price.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">2</span>
                    <p>Chokbazar reviews the draft before it becomes visible in the shop.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">3</span>
                    <p>After approval, the product appears in your product list.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">My AI Drafts</h2>
        </div>

        <div class="overflow-x-auto">
            @if($drafts->count() > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Image</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">AI Title</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Price Range</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Market Source</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Submitted</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drafts as $draft)
                            @php
                                $firstImage = $draft->images[0]['local_path'] ?? null;
                                $priceRange = $draft->sku_data['price_range_bdt'] ?? null;
                                $marketSources = collect($draft->sku_data['market_sources'] ?? [])->filter()->values();
                                $statusClasses = match ($draft->status) {
                                    'approved' => 'bg-emerald-50',
                                    'rejected' => 'bg-red-50',
                                    'failed' => 'bg-amber-50',
                                    'ready_for_review' => 'bg-yellow-50',
                                    default => 'bg-blue-50',
                                };
                                $badgeClasses = match ($draft->status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'failed' => 'bg-amber-100 text-amber-800',
                                    'ready_for_review' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-blue-100 text-blue-700',
                                };
                                $statusLabel = match ($draft->status) {
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'failed' => 'Failed',
                                    'ready_for_review' => 'Pending approval',
                                    default => 'Processing',
                                };
                            @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 {{ $statusClasses }}">
                                <td class="px-6 py-4">
                                    @if($firstImage)
                                        <img src="{{ asset('storage/' . $firstImage) }}" alt="Product draft" class="h-14 w-14 rounded-lg object-cover">
                                    @else
                                        <div class="h-14 w-14 rounded-lg bg-gray-100"></div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $draft->title_en ?: 'Draft processing' }}</p>
                                    <p class="text-xs text-gray-500">{{ $draft->product ? 'Published product #' . $draft->product->id : $draft->source_offer_id }}</p>
                                    @if($draft->price_bdt)
                                        <p class="mt-1 text-xs font-semibold text-gray-700">Suggested: {{ taka($draft->price_bdt) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($draft->status === 'failed' && $draft->error_message)
                                        <p class="mt-2 max-w-xs text-xs text-red-700">{{ $draft->error_message }}</p>
                                    @elseif($draft->status === 'rejected')
                                        <p class="mt-2 text-xs text-red-700">Not approved by Chokbazar review.</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(is_array($priceRange) && filled($priceRange['min'] ?? null) && filled($priceRange['max'] ?? null))
                                        <span class="font-medium text-gray-900">{{ taka($priceRange['min']) }} - {{ taka($priceRange['max']) }}</span>
                                    @else
                                        <span class="text-gray-500">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($marketSources->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($marketSources->take(2) as $source)
                                                <a href="{{ $source }}" target="_blank" rel="noopener noreferrer" class="block max-w-[220px] truncate text-brand-600 hover:text-brand-700 hover:underline">
                                                    {{ parse_url($source, PHP_URL_HOST) ?: 'Market source' }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-500">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $draft->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if($draft->status === 'failed')
                                        <form method="POST" action="{{ route('seller.ai-products.retry', $draft) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-600">Retry</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('seller.ai-products.destroy', $draft) }}" class="inline" onsubmit="return confirm('Delete this draft?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-2 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-10 text-center text-sm text-gray-500">No AI drafts yet.</div>
            @endif
        </div>

        @if($drafts->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $drafts->links() }}
            </div>
        @endif
    </div>
</x-seller-layout>

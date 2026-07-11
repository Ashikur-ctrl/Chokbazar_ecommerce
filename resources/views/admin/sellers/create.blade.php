<x-admin-layout title="Create Seller">
    <div class="max-w-2xl mx-auto">
        <div class="rounded-card border border-gray-100 bg-white p-8 shadow-card">
            <h2 class="text-xl font-bold text-gray-900 mb-6">New Seller</h2>
            <form method="POST" action="{{ route('admin.sellers.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-900">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-900">Phone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-slate-900">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-900">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">{{ old('description') }}</textarea>
                    </div>

                    <!-- Fulfillment Method -->
                    <div>
                        <label for="fulfillment_method" class="block text-sm font-semibold text-slate-900">Fulfillment Method *</label>
                        <select name="fulfillment_method" id="fulfillment_method" required
                                class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                            <option value="">Select Method</option>
                            <option value="email" {{ old('fulfillment_method') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="api" {{ old('fulfillment_method') === 'api' ? 'selected' : '' }}>API</option>
                            <option value="csv" {{ old('fulfillment_method') === 'csv' ? 'selected' : '' }}>CSV Export</option>
                        </select>
                        @error('fulfillment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commission Percentage -->
                    <div>
                        <label for="commission_percentage" class="block text-sm font-semibold text-slate-900">Commission Percentage (%)</label>
                        <input type="number" name="commission_percentage" id="commission_percentage" value="{{ old('commission_percentage', 10) }}" step="0.01" min="0" max="100"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-semibold text-slate-900">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-semibold text-slate-900">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state" class="block text-sm font-semibold text-slate-900">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label for="postal_code" class="block text-sm font-semibold text-slate-900">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-semibold text-slate-900">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country') }}"
                               class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2 font-semibold text-white hover:bg-brand-700">
                            Create Seller
                        </button>
                        <a href="{{ route('admin.sellers.index') }}" class="rounded-lg border border-slate-300 px-6 py-2 font-semibold text-slate-900 hover:bg-slate-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
    </div>
</x-admin-layout>

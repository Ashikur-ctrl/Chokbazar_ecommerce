<x-seller-layout>
    <x-slot:title>Profile</x-slot:title>
    <x-slot:heading>Profile Settings</x-slot:heading>
    <x-slot:subheading><p class="text-sm text-gray-500">Manage your seller profile and business information</p></x-slot:subheading>

    <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-8">
        @csrf @method('PATCH')

        <!-- Branding -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Branding</h2>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                    @if($seller->logo_url)
                        <div class="mb-2">
                            <img src="{{ $seller->logo_url }}" alt="Logo" class="w-20 h-20 rounded-lg object-cover border">
                        </div>
                    @endif
                    <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image</label>
                    @if($seller->cover_url)
                        <div class="mb-2">
                            <img src="{{ $seller->cover_url }}" alt="Cover" class="w-full h-20 rounded-lg object-cover border">
                        </div>
                    @endif
                    <input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $seller->phone) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                    <input id="whatsapp_number" name="whatsapp_number" type="text" value="{{ old('whatsapp_number', $seller->whatsapp_number) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Business Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Business Information</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                    <input id="company_name" name="company_name" type="text" value="{{ old('company_name', $seller->company_name) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">About Your Business</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $seller->description) }}</textarea>
                </div>
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700">Business Type</label>
                    <select id="business_type" name="business_type"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Select type</option>
                        <option value="manufacturer" @selected(old('business_type', $seller->business_type) === 'manufacturer')>Manufacturer</option>
                        <option value="distributor" @selected(old('business_type', $seller->business_type) === 'distributor')>Distributor</option>
                        <option value="retailer" @selected(old('business_type', $seller->business_type) === 'retailer')>Retailer</option>
                        <option value="wholesaler" @selected(old('business_type', $seller->business_type) === 'wholesaler')>Wholesaler</option>
                        <option value="dropshipper" @selected(old('business_type', $seller->business_type) === 'dropshipper')>Dropshipper</option>
                    </select>
                </div>
                <div>
                    <label for="year_established" class="block text-sm font-medium text-gray-700">Year Established</label>
                    <input id="year_established" name="year_established" type="text" value="{{ old('year_established', $seller->year_established) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="website_url" class="block text-sm font-medium text-gray-700">Website</label>
                    <input id="website_url" name="website_url" type="url" value="{{ old('website_url', $seller->website_url) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="return_policy" class="block text-sm font-medium text-gray-700">Return Policy</label>
                    <textarea id="return_policy" name="return_policy" rows="2"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('return_policy', $seller->return_policy) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Address</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="2"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('address', $seller->address) }}</textarea>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $seller->city) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">State / Division</label>
                    <input id="state" name="state" type="text" value="{{ old('state', $seller->state) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                    <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $seller->postal_code) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                    <input id="country" name="country" type="text" value="{{ old('country', $seller->country ?: 'Bangladesh') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Shipping</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="shipping_days_min" class="block text-sm font-medium text-gray-700">Min Shipping Days</label>
                    <input id="shipping_days_min" name="shipping_days_min" type="number" min="1" max="30"
                           value="{{ old('shipping_days_min', $seller->shipping_days_min) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="shipping_days_max" class="block text-sm font-medium text-gray-700">Max Shipping Days</label>
                    <input id="shipping_days_max" name="shipping_days_max" type="number" min="1" max="30"
                           value="{{ old('shipping_days_max', $seller->shipping_days_max) }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Password Change -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-500">Leave blank to keep current password.</p>
            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input id="current_password" name="current_password" type="password"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input id="new_password" name="new_password" type="password"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Save Changes</button>
        </div>
    </form>
</x-seller-layout>

<x-guest-layout>
    <div class="max-w-lg mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Become a Seller</h1>
            <p class="mt-2 text-gray-600">Start selling on {{ config('app.name') }}</p>
        </div>

        <form method="POST" action="{{ route('seller.register') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 space-y-6">
            @csrf

            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Account Information</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div class="border-t pt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Business Information</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company / Shop Name *</label>
                        <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">About Your Business</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700">Business Type</label>
                        <select id="business_type" name="business_type"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Select type</option>
                            <option value="manufacturer" @selected(old('business_type') === 'manufacturer')>Manufacturer</option>
                            <option value="distributor" @selected(old('business_type') === 'distributor')>Distributor</option>
                            <option value="retailer" @selected(old('business_type') === 'retailer')>Retailer</option>
                            <option value="wholesaler" @selected(old('business_type') === 'wholesaler')>Wholesaler</option>
                            <option value="dropshipper" @selected(old('business_type') === 'dropshipper')>Dropshipper</option>
                        </select>
                    </div>
                    <div>
                        <label for="year_established" class="block text-sm font-medium text-gray-700">Year Established</label>
                        <input id="year_established" name="year_established" type="text" value="{{ old('year_established') }}" placeholder="e.g. 2023"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700">Website</label>
                        <input id="website_url" name="website_url" type="url" value="{{ old('website_url') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                        <input id="whatsapp_number" name="whatsapp_number" type="text" value="{{ old('whatsapp_number') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div class="border-t pt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Address</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea id="address" name="address" rows="2"
                                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State / Division</label>
                        <input id="state" name="state" type="text" value="{{ old('state') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input id="country" name="country" type="text" value="{{ old('country', 'Bangladesh') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">
                By registering, you agree to our <a href="#" class="text-brand-600 hover:underline">Terms of Service</a>.
                Your account will be reviewed by an admin before you can start selling.
            </div>

            <button type="submit" class="w-full rounded-lg bg-brand-600 px-6 py-3 text-sm font-bold text-white hover:bg-brand-700 transition">
                Submit Registration
            </button>

            <p class="text-center text-sm text-gray-500">
                Already have a seller account?
                <a href="{{ route('seller.login') }}" class="text-brand-600 hover:underline font-medium">Log in</a>
            </p>
        </form>
    </div>
</x-guest-layout>

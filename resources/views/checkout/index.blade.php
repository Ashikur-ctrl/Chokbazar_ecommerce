<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Checkout - {{ config('app.name', 'E-Commerce') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('shop.index') }}" class="text-xl font-bold text-gray-800">{{ config('app.name', 'E-Commerce') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li><a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Shop</a></li>
                            <li><a href="{{ route('cart.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600">Cart</a></li>
                            <li aria-current="page"><span class="ms-1 text-sm font-medium text-gray-500">Checkout</span></li>
                        </ol>
                    </nav>
                </div>

                <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @csrf

                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('customer_name') border-red-500 @enderror" required>
                                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('customer_email') border-red-500 @enderror" required>
                                        @error('customer_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('customer_phone') border-red-500 @enderror" required>
                                        <p class="mt-1 text-xs text-gray-500">Required for delivery and OTP verification</p>
                                        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address with BD District/Upazila -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Address</h2>

                                @auth
                                    @if(auth()->user()->addresses->count() > 0)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Saved Addresses</label>
                                            <div class="space-y-2">
                                                @foreach(auth()->user()->addresses as $addr)
                                                    <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                                        <input type="radio" name="saved_address" value="{{ $addr->id }}"
                                                               data-name="{{ $addr->name }}" data-phone="{{ $addr->phone }}"
                                                               data-district="{{ $addr->district }}" data-upazila="{{ $addr->upazila }}"
                                                               data-address="{{ $addr->address }}"
                                                               class="mt-1 saved-address-radio">
                                                        <div>
                                                            <p class="text-sm font-medium">{{ $addr->label }}</p>
                                                            <p class="text-xs text-gray-500">{{ $addr->name }} — {{ $addr->phone }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ $addr->address }}, {{ $addr->upazila }}, {{ $addr->district }}
                                                            </p>
                                                        </div>
                                                    </label>
                                                @endforeach
                                                <hr class="my-2">
                                            </div>
                                        </div>
                                    @endif
                                @endauth

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="district" class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                                        <select id="district" name="district"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('district') border-red-500 @enderror" required>
                                            <option value="">Select District</option>
                                        </select>
                                        @error('district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="upazila" class="block text-sm font-medium text-gray-700 mb-1">Upazila/Area *</label>
                                        <select id="upazila" name="upazila"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('upazila') border-red-500 @enderror" required>
                                            <option value="">Select Upazila</option>
                                        </select>
                                        @error('upazila')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Full Address (Road, Area, Building) *</label>
                                        <textarea id="shipping_address" name="shipping_address" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_address') border-red-500 @enderror" required>{{ old('shipping_address') }}</textarea>
                                        @error('shipping_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Timeline Disclosure -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                            <strong>Delivery Information:</strong>
                            Estimated delivery within 3-7 business days across Bangladesh.
                            Same-day delivery available in Dhaka for orders above {{ taka(config('shop.same_day_threshold', 2000)) }}.
                            @if(config('shop.return_policy'))
                                <p class="mt-1 text-xs">Refund/Return: {{ config('shop.return_policy') }}</p>
                            @endif
                        </div>

                        <!-- Payment Methods -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h2>
                                <div class="space-y-4">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="payment_method" value="cod" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">Cash on Delivery (COD)</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Pay when you receive your order. OTP verification required.</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="payment_method" value="sslcommerz" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">SSLCommerz</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Pay via cards, mobile banking, or internet banking</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="payment_method" value="bkash" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">bKash</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Pay directly with your bKash account</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                        <input type="radio" name="payment_method" value="nagad" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">Nagad</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Pay directly with your Nagad account</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary (same as before) -->
                    <div class="space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                                <div class="space-y-3 mb-4">
                                    @foreach($cart->items as $item)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><span class="text-xs text-gray-500">No img</span></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ taka($item->subtotal) }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-t border-gray-200 pt-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="text-gray-900">{{ taka($cart->subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="text-gray-900">{{ taka($cart->shipping_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="text-gray-900">{{ taka($cart->tax_amount) }}</span>
                                    </div>
                                    @if($cart->discount_amount > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Discount</span>
                                            <span class="text-green-600">-{{ taka($cart->discount_amount) }}</span>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-medium">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-gray-900">{{ taka($cart->grand_total) }}</span>
                                    </div>
                                </div>

                                @if(config('shop.return_policy'))
                                    <div class="mt-4 p-3 bg-gray-50 rounded-md text-xs text-gray-500">
                                        {{ config('shop.return_policy') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition-colors">
                            Place Order
                        </button>

                        <div class="text-center">
                            <a href="{{ route('cart.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Cart</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Load districts and upazilas via Alpine -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const districtSelect = document.getElementById('district');
            const upazilaSelect = document.getElementById('upazila');

            // Fetch districts
            fetch('/api/locations/districts')
                .then(r => r.json())
                .then(districts => {
                    districts.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        districtSelect.appendChild(opt);
                    });
                });

            // Fetch upazilas when district changes
            districtSelect.addEventListener('change', function () {
                const district = this.value;
                upazilaSelect.innerHTML = '<option value="">Loading...</option>';

                if (!district) {
                    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                    return;
                }

                fetch('/api/locations/upazilas?district=' + encodeURIComponent(district))
                    .then(r => r.json())
                    .then(upazilas => {
                        upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                        upazilas.forEach(u => {
                            const opt = document.createElement('option');
                            opt.value = u;
                            opt.textContent = u;
                            upazilaSelect.appendChild(opt);
                        });
                    });
            });

            // Saved address autofill
            document.querySelectorAll('.saved-address-radio').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (!this.checked) return;
                    document.getElementById('customer_name').value = this.dataset.name;
                    document.getElementById('customer_phone').value = this.dataset.phone;
                    document.getElementById('shipping_address').value = this.dataset.address;

                    // Set district and trigger upazila load
                    const district = this.dataset.district;
                    if (district) {
                        // Wait for districts to load, then set
                        const waitForDistrict = setInterval(() => {
                            const options = districtSelect.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === district) {
                                    districtSelect.value = district;
                                    districtSelect.dispatchEvent(new Event('change'));
                                    clearInterval(waitForDistrict);

                                    // Set upazila after load
                                    const upazila = this.dataset.upazila;
                                    if (upazila) {
                                        const waitForUpazila = setInterval(() => {
                                            const uOptions = upazilaSelect.options;
                                            for (let j = 0; j < uOptions.length; j++) {
                                                if (uOptions[j].value === upazila) {
                                                    upazilaSelect.value = upazila;
                                                    clearInterval(waitForUpazila);
                                                    break;
                                                }
                                            }
                                        }, 100);
                                        setTimeout(() => clearInterval(waitForUpazila), 5000);
                                    }
                                    break;
                                }
                            }
                        }, 100);
                        setTimeout(() => clearInterval(waitForDistrict), 5000);
                    }
                });
            });
        });
    </script>
</body>
</html>

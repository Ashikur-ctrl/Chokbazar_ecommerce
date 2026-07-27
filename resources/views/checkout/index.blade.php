<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Checkout - {{ config('app.name', 'E-Commerce') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm+serif+display:400&family=dm+sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function(){const d=localStorage.getItem('darkMode')==='true'||(!('darkMode'in localStorage)&&window.matchMedia('(prefers-color-scheme:dark)').matches);if(d)document.documentElement.classList.add('dark');})();
    </script>
</head>
<body class="font-sans antialiased bg-[#f6f1ec] text-[#1a1a1a]">
    <div class="min-h-screen">
        <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100/80 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('shop.index') }}" class="text-xl font-bold text-[#1a1a1a] font-display tracking-tight">Chokbazar</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Step Progress -->
                <div class="mb-10">
                    <div class="flex items-center justify-between max-w-lg mx-auto">
                        <div class="step">
                            <div class="step-indicator completed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-medium text-brand-700 hidden sm:inline">Cart</span>
                        </div>
                        <div class="step-line active"></div>
                        <div class="step">
                            <div class="step-indicator active">2</div>
                            <span class="text-sm font-medium text-brand-600 hidden sm:inline">Checkout</span>
                        </div>
                        <div class="step-line pending"></div>
                        <div class="step">
                            <div class="step-indicator pending">3</div>
                            <span class="text-sm font-medium text-gray-400 hidden sm:inline">Confirmation</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    @csrf

                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Customer Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="customer_name" class="block text-sm font-medium text-[#6b6b6b] mb-1">Full Name *</label>
                                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                               class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('customer_name') border-red-500 @enderror" required>
                                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="customer_email" class="block text-sm font-medium text-[#6b6b6b] mb-1">Email Address *</label>
                                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                               class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('customer_email') border-red-500 @enderror" required>
                                        @error('customer_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="customer_phone" class="block text-sm font-medium text-[#6b6b6b] mb-1">Phone Number *</label>
                                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                               class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('customer_phone') border-red-500 @enderror" required>
                                        <p class="mt-1 text-xs text-gray-500">Required for delivery and OTP verification</p>
                                        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address with BD District/Upazila -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Shipping Address</h2>

                                @auth
                                    @if(auth()->user()->addresses->count() > 0)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Saved Addresses</label>
                                            <div class="space-y-2">
                                                @foreach(auth()->user()->addresses as $addr)
                                                    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30">
                                                        <input type="radio" name="saved_address" value="{{ $addr->id }}"
                                                               data-name="{{ $addr->name }}" data-phone="{{ $addr->phone }}"
                                                               data-district="{{ $addr->district }}" data-upazila="{{ $addr->upazila }}"
                                                               data-address="{{ $addr->address }}"
                                                               class="mt-1 saved-address-radio text-brand-600 focus:ring-brand-500">
                                                        <div>
                                                            <p class="text-sm font-medium text-[#1a1a1a]">{{ $addr->label }}</p>
                                                            <p class="text-xs text-[#6b6b6b]">{{ $addr->name }} — {{ $addr->phone }}</p>
                                                            <p class="text-xs text-[#6b6b6b]">
                                                                {{ $addr->address }}, {{ $addr->upazila }}, {{ $addr->district }}
                                                            </p>
                                                        </div>
                                                    </label>
                                                @endforeach
                                                <hr class="my-2 border-gray-100">
                                            </div>
                                        </div>
                                    @endif
                                @endauth

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="district" class="block text-sm font-medium text-[#6b6b6b] mb-1">District *</label>
                                        <select id="district" name="district"
                                                class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('district') border-red-500 @enderror" required>
                                            <option value="">Select District</option>
                                        </select>
                                        @error('district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="upazila" class="block text-sm font-medium text-[#6b6b6b] mb-1">Upazila/Area *</label>
                                        <select id="upazila" name="upazila"
                                                class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('upazila') border-red-500 @enderror" required>
                                            <option value="">Select Upazila</option>
                                        </select>
                                        @error('upazila')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="shipping_address" class="block text-sm font-medium text-[#6b6b6b] mb-1">Full Address (Road, Area, Building) *</label>
                                        <textarea id="shipping_address" name="shipping_address" rows="3"
                                                  class="w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500 @error('shipping_address') border-red-500 @enderror" required>{{ old('shipping_address') }}</textarea>
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
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Payment Method</h2>
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30 transition-colors">
                                        <input type="radio" name="payment_method" value="cod" checked class="h-4 w-4 text-brand-600 focus:ring-brand-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-semibold text-[#1a1a1a]">Cash on Delivery (COD)</span>
                                            <p class="text-xs text-[#6b6b6b] mt-0.5">Pay when you receive your order. OTP verification required.</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30 transition-colors">
                                        <input type="radio" name="payment_method" value="sslcommerz" class="h-4 w-4 text-brand-600 focus:ring-brand-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-semibold text-[#1a1a1a]">SSLCommerz</span>
                                            <p class="text-xs text-[#6b6b6b] mt-0.5">Pay via cards, mobile banking, or internet banking</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30 transition-colors">
                                        <input type="radio" name="payment_method" value="bkash" class="h-4 w-4 text-brand-600 focus:ring-brand-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-semibold text-[#1a1a1a]">bKash</span>
                                            <p class="text-xs text-[#6b6b6b] mt-0.5">Pay directly with your bKash account</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/30 transition-colors">
                                        <input type="radio" name="payment_method" value="nagad" class="h-4 w-4 text-brand-600 focus:ring-brand-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-semibold text-[#1a1a1a]">Nagad</span>
                                            <p class="text-xs text-[#6b6b6b] mt-0.5">Pay directly with your Nagad account</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary (same as before) -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-[#1a1a1a] mb-4">Order Summary</h2>
                                <div class="space-y-3 mb-4">
                                    @foreach($cart->items as $item)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center"><span class="text-xs text-[#6b6b6b]">No img</span></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-[#1a1a1a] truncate">{{ $item->product->name }}</p>
                                                <p class="text-sm text-[#6b6b6b]">Qty: {{ $item->quantity }}</p>
                                            </div>
                                            <div class="text-sm font-semibold text-[#1a1a1a]">{{ taka($item->subtotal) }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-t border-gray-100 pt-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Subtotal</span>
                                        <span class="text-[#1a1a1a]">{{ taka($cart->subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Shipping</span>
                                        <span class="text-[#1a1a1a]">{{ taka($cart->shipping_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b6b6b]">Tax</span>
                                        <span class="text-[#1a1a1a]">{{ taka($cart->tax_amount) }}</span>
                                    </div>
                                    @if($cart->discount_amount > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-[#6b6b6b]">Discount</span>
                                            <span class="text-emerald-600">-{{ taka($cart->discount_amount) }}</span>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-100 pt-2 flex justify-between text-lg font-bold">
                                        <span class="text-[#1a1a1a]">Total</span>
                                        <span class="text-brand-600">{{ taka($cart->grand_total) }}</span>
                                    </div>
                                </div>

                                @if(config('shop.return_policy'))
                                    <div class="mt-4 p-3 rounded-xl bg-gray-50 text-xs text-[#6b6b6b]">
                                        {{ config('shop.return_policy') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3.5 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 active:scale-[0.98] shadow-card">
                            Place Order — {{ taka($cart->grand_total) }}
                        </button>

                        <div class="text-center">
                            <a href="{{ route('cart.index') }}" class="text-sm text-[#6b6b6b] hover:text-brand-600 transition-colors">← Back to Cart</a>
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

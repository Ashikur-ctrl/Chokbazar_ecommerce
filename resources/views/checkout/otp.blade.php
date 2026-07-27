<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#1a1a1a] leading-tight">Verify Your Order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card">
                <div class="p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-[#1a1a1a] mb-2">OTP Verification</h3>
                    <p class="text-[#6b6b6b] mb-6">
                        We've sent a 6-digit OTP to <strong>{{ $order->customer_phone }}</strong>.
                        <br>Enter it below to confirm your COD order.
                    </p>

                    <form method="POST" action="{{ route('orders.otp.verify', $order) }}" class="space-y-4">
                        @csrf
                        <div>
                            <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="off" autofocus required
                                   class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 border border-gray-200 rounded-xl focus:border-brand-500 focus:ring-brand-500 @error('otp') border-red-500 @enderror"
                                   placeholder="000000">
                            @error('otp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 text-white font-bold py-3 px-4 hover:from-brand-700 hover:to-brand-800 transition-all duration-200 active:scale-[0.98] shadow-card">
                            Verify OTP & Confirm Order
                        </button>
                    </form>

                    <div class="mt-6">
                        <p class="text-sm text-[#6b6b6b]">
                            Didn't receive the code?
                            <form method="POST" action="{{ route('orders.otp.resend', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-brand-600 hover:text-brand-700 font-semibold">Resend OTP</button>
                            </form>
                        </p>
                    </div>

                    <div class="mt-4 text-xs text-[#6b6b6b]">
                        Order: {{ $order->order_number }} |
                        Total: {{ taka($order->total_amount) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

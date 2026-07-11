<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verify Your Order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">OTP Verification</h3>
                    <p class="text-gray-600 mb-6">
                        We've sent a 6-digit OTP to <strong>{{ $order->customer_phone }}</strong>.
                        <br>Enter it below to confirm your COD order.
                    </p>

                    <form method="POST" action="{{ route('orders.otp.verify', $order) }}" class="space-y-4">
                        @csrf
                        <div>
                            <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="off" autofocus required
                                   class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('otp') border-red-500 @enderror"
                                   placeholder="000000">
                            @error('otp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            Verify OTP & Confirm Order
                        </button>
                    </form>

                    <div class="mt-6">
                        <p class="text-sm text-gray-500">
                            Didn't receive the code?
                            <form method="POST" action="{{ route('orders.otp.resend', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-medium">Resend OTP</button>
                            </form>
                        </p>
                    </div>

                    <div class="mt-4 text-xs text-gray-400">
                        Order: {{ $order->order_number }} |
                        Total: {{ taka($order->total_amount) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

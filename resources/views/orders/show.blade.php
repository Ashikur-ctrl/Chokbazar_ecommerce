<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $order->order_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('orders.tracking', $order) }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">Track order</a>
                <a href="{{ route('orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">My orders</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Order status</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($order->status) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Payment</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($order->payment_status) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total</p>
                        <p class="font-medium text-gray-900">{{ taka($order->total_amount) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="p-6 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-sm text-gray-500">Qty {{ $item->quantity }} - {{ taka($item->price) }}</p>
                            </div>
                            <p class="font-medium text-gray-900">{{ taka($item->total) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-3">Shipping Address</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $order->shipping_address }}</p>
                </div>
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-3">Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ taka($order->subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>{{ taka($order->shipping_amount) }}</span></div>
                        <div class="flex justify-between"><span>Tax</span><span>{{ taka($order->tax_amount) }}</span></div>
                        <div class="flex justify-between border-t pt-2 font-semibold text-gray-900"><span>Total</span><span>{{ taka($order->total_amount) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

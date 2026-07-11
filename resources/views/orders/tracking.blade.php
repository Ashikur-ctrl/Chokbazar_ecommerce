<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-orange-600">Order tracking</p>
            <h2 class="text-2xl font-extrabold text-slate-950">{{ $order->order_number }}</h2>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @php($steps = ['pending' => 'Order placed', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered'])
            @php($currentIndex = array_search($order->status, array_keys($steps)))
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-orange-100">
                <div class="space-y-5">
                    @foreach($steps as $status => $label)
                        @php($done = $currentIndex !== false && $loop->index <= $currentIndex)
                        <div class="flex gap-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $done ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-400' }}">{{ $loop->iteration }}</div>
                            <div>
                                <p class="font-bold {{ $done ? 'text-slate-950' : 'text-slate-400' }}">{{ $label }}</p>
                                <p class="text-sm text-slate-500">{{ $status === $order->status ? 'Current status' : 'Order pipeline step' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($order->tracking_number)
                    <div class="mt-6 rounded-md bg-orange-50 p-4 text-sm text-slate-700">
                        Courier: <strong>{{ $order->courier_name ?: 'N/A' }}</strong> · Tracking: <strong>{{ $order->tracking_number }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

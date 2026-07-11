<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Return Requests</h2>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">My Orders</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif

            @forelse($returnRequests as $return)
                <div class="bg-white shadow-sm sm:rounded-lg mb-4 overflow-hidden">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">Return Request #{{ $return->id }}</p>
                            <p class="text-sm text-gray-500 mt-1">Order: <a href="{{ route('orders.show', $return->order) }}" class="text-brand-600 hover:text-brand-800">{{ $return->order->order_number }}</a></p>
                            <p class="text-sm text-gray-500">Reason: {{ $return->reason }}</p>
                            @if($return->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $return->description }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <x-badge :variant="match($return->status) {
                                'pending' => 'warning',
                                'approved' => 'info',
                                'rejected' => 'danger',
                                'refunded' => 'success',
                                default => 'neutral'
                            }">{{ ucfirst($return->status) }}</x-badge>
                            <p class="text-xs text-gray-500 mt-1">{{ $return->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <p class="text-gray-500 mb-4">You haven't submitted any return requests yet.</p>
                        <a href="{{ route('orders.index') }}" class="inline-block rounded-lg bg-brand-600 px-6 py-2 text-sm font-semibold text-white hover:bg-brand-700">View Orders</a>
                    </div>
                </div>
            @endforelse

            @if($returnRequests->hasPages())
                <div class="mt-6">{{ $returnRequests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-admin-layout title="Documents: {{ $seller->company_name ?: $seller->name }}">
    <div class="max-w-3xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @php $docs = $seller->business_documents; @endphp
                @if($docs && count($docs) > 0)
                    <ul class="space-y-3">
                        @foreach($docs as $doc)
                            <li class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-900">{{ $doc['name'] ?? 'Document' }}</span>
                                <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" class="text-sm text-brand-600 hover:underline">View</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-center py-8">No documents uploaded.</p>
                @endif

                <div class="mt-6 border-t pt-6 space-y-3">
                    <h3 class="font-bold text-gray-900">Verification</h3>
                    @if($seller->verification_status === 'pending')
                        <div class="flex gap-3">
                            <form method="POST" action="{{ route('admin-legacy.sellers.approve', $seller) }}">
                                @csrf
                                <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin-legacy.sellers.reject', $seller) }}">
                                @csrf
                                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Reject</button>
                            </form>
                        </div>
                    @else
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                            {{ $seller->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($seller->verification_status) }}
                        </span>
                    @endif
                </div>
        </div>
    </div>
</x-admin-layout>

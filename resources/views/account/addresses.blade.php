<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-orange-600">Customer account</p>
            <h2 class="text-2xl font-extrabold text-slate-950">Address Book</h2>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
            <form method="POST" action="{{ route('addresses.store') }}" class="rounded-lg border border-orange-100 bg-white p-6 shadow-sm">
                @csrf
                <h3 class="text-lg font-bold text-slate-950">Save New Address</h3>
                <div class="mt-4 grid gap-3">
                    <input name="label" value="Home" class="rounded-md border-orange-200" placeholder="Label">
                    <input name="name" class="rounded-md border-orange-200" placeholder="Recipient name" required>
                    <input name="phone" class="rounded-md border-orange-200" placeholder="Phone">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="city" class="rounded-md border-orange-200" placeholder="City">
                        <input name="area" class="rounded-md border-orange-200" placeholder="Area">
                    </div>
                    <textarea name="address" rows="4" class="rounded-md border-orange-200" placeholder="Full address" required></textarea>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1" class="rounded border-orange-300 text-orange-600"> Default address</label>
                    <button class="rounded-md bg-orange-600 px-4 py-2 text-sm font-bold text-white hover:bg-orange-700">Save address</button>
                </div>
            </form>

            <div class="grid gap-4">
                @forelse($addresses as $address)
                    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-bold text-slate-950">{{ $address->label }} @if($address->is_default)<span class="ml-2 rounded bg-orange-100 px-2 py-1 text-xs text-orange-700">Default</span>@endif</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $address->name }} · {{ $address->phone }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $address->address }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $address->area }} {{ $address->city }}</p>
                            </div>
                            <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-bold text-rose-600">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600">No saved address yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

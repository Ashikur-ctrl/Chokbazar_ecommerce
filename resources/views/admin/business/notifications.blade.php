<x-admin-layout title="Notifications">
    <div class="space-y-6">
        <!-- Alert Cards -->
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $lowStockCount }}</p>
                </div>
            </div>
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $pendingOrders }}</p>
                </div>
            </div>
            <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Unread Messages</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $contactMessages }}</p>
                </div>
            </div>
        </div>

        <!-- Notification Feed -->
        <div class="rounded-card border border-gray-100 bg-white shadow-card">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-bold text-gray-900">Notification Feed</h3></div>
            <div class="divide-y divide-gray-50">
                @forelse($notifications as $n)
                    <div class="p-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600 shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">{{ $n->message ?? 'Notification' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500 text-center">No notifications yet.</p>
                @endforelse
            </div>
            @if($notifications->hasPages())<div class="p-6 border-t">{{ $notifications->links() }}</div>@endif
        </div>
    </div>
</x-admin-layout>

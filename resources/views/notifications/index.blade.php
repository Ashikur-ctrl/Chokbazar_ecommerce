@extends('layouts.app')

@section('title', 'Notifications - ' . config('app.name'))

@section('content')
<div class="py-10">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Stay updated with your orders, marketplace activity, and system alerts.</p>
            </div>
            @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 transition-colors">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <!-- Filters -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto hide-scrollbar">
            <a href="{{ route('notifications') }}"
               class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ !request('type') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
                All
            </a>
            @foreach(['order' => 'Orders', 'system' => 'System', 'promotion' => 'Promotions', 'alert' => 'Alerts'] as $type => $label)
                <a href="{{ route('notifications', ['type' => $type]) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ request('type') === $type ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- List -->
        @if($notifications->count() > 0)
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <div class="group relative rounded-xl border border-gray-100 dark:border-gray-800 p-5 transition-all duration-200
                                {{ is_null($notification->read_at) ? 'bg-brand-50/50 dark:bg-brand-950/20 border-l-4 border-l-brand-500' : 'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                        {{ $notification->type === 'order' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400' : '' }}
                                        {{ $notification->type === 'system' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400' : '' }}
                                        {{ $notification->type === 'promotion' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400' : '' }}
                                        {{ $notification->type === 'alert' ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400' : '' }}
                                        {{ !in_array($notification->type, ['order','system','promotion','alert']) ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}">
                                @switch($notification->type)
                                    @case('order')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        @break
                                    @case('system')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @break
                                    @case('promotion')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                        @break
                                    @case('alert')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @endswitch
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification->message }}</p>
                                    </div>
                                    <span class="shrink-0 text-[11px] text-gray-400 dark:text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                @if($notification->url)
                                    <a href="{{ $notification->url }}"
                                       class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-brand-600 dark:text-brand-400 hover:text-brand-700 transition-colors">
                                        View details
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endif
                            </div>
                            <!-- Unread indicator -->
                            @if(is_null($notification->read_at))
                                <span class="absolute top-5 right-5 w-2 h-2 rounded-full bg-brand-500"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty state -->
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No notifications yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">We'll let you know when something new arrives.</p>
                <a href="{{ route('shop.index') }}" class="inline-block mt-6 px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-colors">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

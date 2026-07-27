@extends('layouts.app')

@section('title', 'Activity Log - ' . config('app.name'))

@section('content')
<div class="py-10">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Log</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track all changes and actions across the system.</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="flex items-center gap-3 mb-6 flex-wrap">
            <select name="event" onchange="this.form.submit()"
                    class="rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm py-2.5">
                <option value="">All Events</option>
                @foreach(['created', 'updated', 'deleted', 'login', 'logout', 'order_placed', 'payment_received', 'fulfilled'] as $ev)
                    <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>{{ ucfirst($ev) }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-400 dark:text-gray-500">
                {{ $activities->total() }} {{ Str::plural('entry', $activities->total()) }}
            </span>
            @if(request()->anyFilled(['event', 'user_id']))
                <a href="{{ route('audit-log') }}" class="text-sm text-brand-600 dark:text-brand-400 hover:underline">Clear filters</a>
            @endif
        </form>

        @if($activities->count() > 0)
            <div class="space-y-1">
                @foreach($activities as $activity)
                    <div class="flex items-start gap-4 px-5 py-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <!-- Timeline dot -->
                        <div class="relative flex flex-col items-center pt-1.5">
                            <div class="w-2.5 h-2.5 rounded-full
                                {{ $activity->event === 'created' ? 'bg-emerald-500' : '' }}
                                {{ $activity->event === 'updated' ? 'bg-blue-500' : '' }}
                                {{ $activity->event === 'deleted' ? 'bg-rose-500' : '' }}
                                {{ !in_array($activity->event, ['created','updated','deleted']) ? 'bg-gray-400' : '' }}">
                            </div>
                            @if(!$loop->last)
                                <div class="w-px flex-1 bg-gray-200 dark:bg-gray-700 mt-1"></div>
                            @endif
                        </div>
                        <!-- Content -->
                        <div class="flex-1 min-w-0 pb-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        <span class="font-semibold">{{ $activity->causer?->name ?? 'System' }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">
                                            {{ $activity->description }}
                                            @if($activity->subject_type)
                                                <span class="text-gray-400 dark:text-gray-500 font-mono text-xs">
                                                    {{ class_basename($activity->subject_type) }}#{{ $activity->subject_id }}
                                                </span>
                                            @endif
                                        </span>
                                    </p>
                                    @if($activity->properties && $activity->properties->count() > 0)
                                        <details class="mt-1 group">
                                            <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300">Show details</summary>
                                            <pre class="mt-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg overflow-x-auto max-h-48">{{ $activity->properties->toJson(JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @endif
                                </div>
                                <span class="shrink-0 text-[11px] text-gray-400 dark:text-gray-500 font-mono whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $activities->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No activity entries yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Activity will appear as actions are performed.</p>
            </div>
        @endif
    </div>
</div>
@endsection

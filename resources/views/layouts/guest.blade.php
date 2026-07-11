<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-brand-50 via-white to-secondary-50">
            <div class="mb-6">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-2xl font-extrabold text-gray-900">
                    <span class="rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 px-2.5 py-1 text-white">BD</span>
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="w-full sm:max-w-md px-8 py-8 bg-white rounded-2xl shadow-card border border-gray-100 animate-fade-in">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

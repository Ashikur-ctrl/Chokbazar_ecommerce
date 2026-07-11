<x-guest-layout>
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">Seller Login</h1>
            <p class="mt-2 text-gray-600">Log in to your seller dashboard</p>
        </div>

        <form method="POST" action="{{ route('seller.login') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 space-y-6">
            @csrf

            @if(session('success'))
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-lg bg-brand-600 px-6 py-3 text-sm font-bold text-white hover:bg-brand-700 transition">
                Log In
            </button>

            <p class="text-center text-sm text-gray-500">
                Don't have a seller account?
                <a href="{{ route('seller.register') }}" class="text-brand-600 hover:underline font-medium">Register here</a>
            </p>
        </form>
    </div>
</x-guest-layout>

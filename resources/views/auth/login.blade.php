<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Welcome Back</h1>
        <p class="mt-1 text-sm text-gray-500">Sign in to your account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-form-group label="Email" name="email" :error="$errors->first('email')">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <x-form-group label="Password" name="password" :error="$errors->first('password')">
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-3 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 shadow-card">
            Sign In
        </button>

        <p class="text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Register</a>
        </p>
    </form>
</x-guest-layout>

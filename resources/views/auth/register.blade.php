<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Create Account</h1>
        <p class="mt-1 text-sm text-gray-500">Join {{ config('app.name') }} today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-form-group label="Name" name="name" :error="$errors->first('name')">
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <x-form-group label="Email" name="email" :error="$errors->first('email')">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <x-form-group label="Password" name="password" :error="$errors->first('password')">
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <x-form-group label="Confirm Password" name="password_confirmation" :error="$errors->first('password_confirmation')">
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </x-form-group>

        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-3 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 shadow-card">
            Create Account
        </button>

        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
        </p>
    </form>
</x-guest-layout>

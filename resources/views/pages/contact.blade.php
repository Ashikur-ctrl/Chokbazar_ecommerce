<x-app-layout>
    @section('title', 'Contact Us - ' . config('app.name'))
    @section('description', 'Get in touch with our support team.')

    <div class="bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" data-animate>
                <h1 class="text-4xl font-extrabold text-gray-900">Contact Us</h1>
                <p class="mt-3 text-lg text-gray-600">We'd love to hear from you. Get in touch with our team.</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2" data-animate>
                <!-- Contact Info -->
                <div class="space-y-6">
                    <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email</h3>
                                <p class="text-sm text-gray-500">support@example.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Phone</h3>
                                <p class="text-sm text-gray-500">+880 1XXX-XXXXXX</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-card border border-gray-100 bg-white p-6 shadow-card">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Address</h3>
                                <p class="text-sm text-gray-500">Dhaka, Bangladesh</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-card">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Send a Message</h2>
                    @if(session('success'))
                        <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
                    @endif
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                        @csrf
                        <x-form-group label="Name" name="name" :error="$errors->first('name')">
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </x-form-group>
                        <x-form-group label="Email" name="email" :error="$errors->first('email')">
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </x-form-group>
                        <x-form-group label="Subject" name="subject" :error="$errors->first('subject')">
                            <input type="text" name="subject" value="{{ old('subject') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </x-form-group>
                        <x-form-group label="Message" name="message" :error="$errors->first('message')">
                            <textarea name="message" rows="4" required
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('message') }}</textarea>
                        </x-form-group>
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-3 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 transition-all duration-200 shadow-card">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

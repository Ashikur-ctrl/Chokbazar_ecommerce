<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegister(): View
    {
        return view('seller.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:sellers,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'business_type' => 'nullable|string|max:100',
            'year_established' => 'nullable|string|max:4',
            'website_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $this->authService->register($validated);

        return redirect()->route('seller.login')
            ->with('success', 'Registration submitted! Your account is pending admin approval. You will be notified when approved.');
    }

    public function showLogin(): View
    {
        return view('seller.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $user = auth()->user();

            if (!$user->isSeller()) {
                auth()->logout();
                return back()->withErrors(['email' => 'These credentials do not have seller access.']);
            }

            if ($user->seller && $user->seller->verification_status === 'pending') {
                auth()->logout();
                return back()->withErrors(['email' => 'Your seller account is pending approval. Please wait for admin verification.']);
            }

            if ($user->seller && $user->seller->verification_status === 'rejected') {
                auth()->logout();
                return back()->withErrors(['email' => 'Your seller account has been rejected. Contact admin for details.']);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('seller.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login');
    }
}

<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $seller = auth()->user()->seller;

        return view('seller.profile.edit', compact('seller'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = $user->seller;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id . '|unique:sellers,email,' . $seller->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'business_type' => 'nullable|string|max:100',
            'year_established' => 'nullable|string|max:4',
            'website_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'return_policy' => 'nullable|string|max:2000',
            'shipping_days_min' => 'nullable|integer|min:1|max:30',
            'shipping_days_max' => 'nullable|integer|min:1|max:30',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        // Handle logo upload
        $logoPath = $seller->logo;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('seller-logos', 'public');
        }

        // Handle cover image upload
        $coverPath = $seller->cover_image;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('seller-covers', 'public');
        }

        // Update seller
        $seller->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $seller->phone,
            'company_name' => $validated['company_name'],
            'description' => $validated['description'] ?? $seller->description,
            'business_type' => $validated['business_type'] ?? $seller->business_type,
            'year_established' => $validated['year_established'] ?? $seller->year_established,
            'website_url' => $validated['website_url'] ?? $seller->website_url,
            'whatsapp_number' => $validated['whatsapp_number'] ?? $seller->whatsapp_number,
            'address' => $validated['address'] ?? $seller->address,
            'city' => $validated['city'] ?? $seller->city,
            'state' => $validated['state'] ?? $seller->state,
            'postal_code' => $validated['postal_code'] ?? $seller->postal_code,
            'country' => $validated['country'] ?? $seller->country,
            'return_policy' => $validated['return_policy'] ?? $seller->return_policy,
            'shipping_days_min' => (int) ($validated['shipping_days_min'] ?? $seller->shipping_days_min),
            'shipping_days_max' => (int) ($validated['shipping_days_max'] ?? $seller->shipping_days_max),
            'logo' => $logoPath,
            'cover_image' => $coverPath,
        ]);

        return redirect()->route('seller.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}

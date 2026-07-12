<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SellerController extends Controller
{
    /**
     * Display a listing of sellers
     */
    public function index(): View
    {
        $sellers = Seller::paginate(15);
        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new seller
     */
    public function create(): View
    {
        return view('admin.sellers.create');
    }

    /**
     * Store a newly created seller in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fulfillment_method' => 'required|in:api,email,csv',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $seller = Seller::create($validated);
        $seller->generateApiKey();

        return redirect()->route('admin-legacy.sellers.show', $seller)
                       ->with('success', 'Seller created successfully');
    }

    /**
     * Display the specified seller
     */
    public function show(Seller $seller): View
    {
        $seller->load('products', 'fulfillmentRequests');
        $stats = app(\App\Services\DropshippingService::class)->getSellerStats($seller);
        return view('admin.sellers.show', compact('seller', 'stats'));
    }

    /**
     * Show the form for editing the specified seller
     */
    public function edit(Seller $seller): View
    {
        return view('admin.sellers.edit', compact('seller'));
    }

    /**
     * Update the specified seller in database
     */
    public function update(Request $request, Seller $seller): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers,email,' . $seller->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fulfillment_method' => 'required|in:api,email,csv',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $seller->update($validated);

        return redirect()->route('admin-legacy.sellers.show', $seller)
                       ->with('success', 'Seller updated successfully');
    }

    /**
     * Remove the specified seller from database
     */
    public function destroy(Seller $seller): RedirectResponse
    {
        $seller->delete();

        return redirect()->route('admin-legacy.sellers.index')
                       ->with('success', 'Seller deleted successfully');
    }

    /**
     * Regenerate API key for seller
     */
    public function regenerateApiKey(Seller $seller): RedirectResponse
    {
        $newKey = $seller->generateApiKey();

        return redirect()->route('admin-legacy.sellers.show', $seller)
                       ->with('success', "API Key regenerated: $newKey");
    }

    /**
     * Toggle seller active status
     */
    public function toggleActive(Seller $seller): RedirectResponse
    {
        $seller->update(['is_active' => !$seller->is_active]);

        return redirect()->back()
                       ->with('success', 'Seller status updated');
    }

    /**
     * Display pending sellers for approval
     */
    public function pendingApproval(): View
    {
        $sellers = Seller::pendingApproval()->latest()->paginate(20);
        return view('admin.sellers.pending', compact('sellers'));
    }

    /**
     * Approve a seller account
     */
    public function approve(Seller $seller): RedirectResponse
    {
        $seller->update([
            'verification_status' => 'verified',
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', "Seller '{$seller->company_name}' has been approved.");
    }

    /**
     * Reject a seller account
     */
    public function reject(Request $request, Seller $seller): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $seller->update([
            'verification_status' => 'rejected',
        ]);

        return redirect()->back()->with('success', "Seller '{$seller->company_name}' has been rejected.");
    }

    /**
     * Show seller documents
     */
    public function showDocuments(Seller $seller): View
    {
        return view('admin.sellers.documents', compact('seller'));
    }

    /**
     * Suspend a seller account
     */
    public function suspend(Request $request, Seller $seller): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $seller->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->reason,
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', "Seller '{$seller->company_name}' has been suspended.");
    }

    /**
     * Restore a suspended seller
     */
    public function restore(Seller $seller): RedirectResponse
    {
        $seller->update([
            'suspended_at' => null,
            'suspension_reason' => null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', "Seller '{$seller->company_name}' has been restored.");
    }
}

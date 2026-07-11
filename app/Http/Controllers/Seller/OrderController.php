<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $sellerId = auth()->user()->seller_id;

        $query = FulfillmentRequest::with('order')
            ->where('seller_id', $sellerId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(FulfillmentRequest $fulfillmentRequest): View
    {
        $this->authorizeSeller($fulfillmentRequest);

        $fulfillmentRequest->load(['order', 'items.product']);

        return view('seller.orders.show', compact('fulfillmentRequest'));
    }

    public function markShipped(Request $request, FulfillmentRequest $fulfillmentRequest): RedirectResponse
    {
        $this->authorizeSeller($fulfillmentRequest);

        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'courier_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $fulfillmentRequest->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $validated['tracking_number'] ?? $fulfillmentRequest->tracking_number,
        ]);

        // Also update the order tracking info
        $fulfillmentRequest->order->update([
            'tracking_number' => $validated['tracking_number'] ?? $fulfillmentRequest->order->tracking_number,
            'courier_name' => $validated['courier_name'] ?? $fulfillmentRequest->order->courier_name,
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return redirect()->route('seller.orders.show', $fulfillmentRequest)
            ->with('success', 'Order marked as shipped.');
    }

    private function authorizeSeller(FulfillmentRequest $fulfillmentRequest): void
    {
        if ($fulfillmentRequest->seller_id !== auth()->user()->seller_id) {
            abort(403, 'This order does not belong to you.');
        }
    }
}

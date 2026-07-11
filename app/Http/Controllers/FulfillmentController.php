<?php

namespace App\Http\Controllers;

use App\Models\FulfillmentRequest;
use App\Models\Order;
use App\Services\DropshippingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FulfillmentController extends Controller
{
    protected DropshippingService $dropshippingService;

    public function __construct(DropshippingService $dropshippingService)
    {
        $this->dropshippingService = $dropshippingService;
    }

    /**
     * Display fulfillment requests for an order
     */
    public function orderFulfillment(Order $order): View
    {
        $fulfillmentRequests = $order->fulfillmentRequests()->with('seller', 'items')->get();
        $status = $this->dropshippingService->getFulfillmentStatus($order);
        
        return view('admin.fulfillment.order', compact('order', 'fulfillmentRequests', 'status'));
    }

    /**
     * Display all fulfillment requests
     */
    public function index(Request $request): View
    {
        $query = FulfillmentRequest::with('order', 'seller');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        $fulfillmentRequests = $query->latest()->paginate(20);
        
        return view('admin.fulfillment.index', compact('fulfillmentRequests'));
    }

    /**
     * Show fulfillment request details
     */
    public function show(FulfillmentRequest $fulfillmentRequest): View
    {
        $fulfillmentRequest->load('order', 'seller', 'items.product');
        return view('admin.fulfillment.show', compact('fulfillmentRequest'));
    }

    /**
     * Confirm fulfillment request
     */
    public function confirm(FulfillmentRequest $fulfillmentRequest): RedirectResponse
    {
        $this->dropshippingService->confirmFulfillment($fulfillmentRequest);
        
        return redirect()->back()
                       ->with('success', 'Fulfillment request confirmed');
    }

    /**
     * Mark fulfillment as shipped
     */
    public function markShipped(Request $request, FulfillmentRequest $fulfillmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $this->dropshippingService->markShipped(
            $fulfillmentRequest,
            $validated['tracking_number'] ?? null
        );
        
        return redirect()->back()
                       ->with('success', 'Fulfillment marked as shipped');
    }

    /**
     * Update fulfillment request status
     */
    public function updateStatus(Request $request, FulfillmentRequest $fulfillmentRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $fulfillmentRequest->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Fulfillment status updated.');
    }

    /**
     * Export fulfillment request to CSV
     */
    public function exportCsv(FulfillmentRequest $fulfillmentRequest)
    {
        $filename = "fulfillment_{$fulfillmentRequest->fulfillment_request_number}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($fulfillmentRequest) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SKU', 'Product Name', 'Quantity', 'Price', 'Total']);
            
            foreach ($fulfillmentRequest->items as $item) {
                fputcsv($file, [
                    $item->sku,
                    $item->product->name,
                    $item->quantity,
                    $item->price,
                    $item->quantity * $item->price,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\FulfillmentRequest;
use App\Models\FulfillmentRequestItem;
use App\Models\Seller;
use App\Mail\FulfillmentRequestNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class DropshippingService
{
    /**
     * Split order by sellers and create fulfillment requests
     */
    public function createFulfillmentRequests(Order $order): EloquentCollection
    {
        // Group order items by seller
        $groupedItems = $order->items->groupBy(function ($item) {
            return $item->product->seller_id;
        });

        $fulfillmentRequests = new EloquentCollection();

        foreach ($groupedItems as $sellerId => $items) {
            $seller = Seller::findOrFail($sellerId ?? 1); // Default to first seller (in-house)
            
            // Calculate total for this seller
            $totalAmount = $items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            // Create fulfillment request
            $fulfillmentRequest = FulfillmentRequest::create([
                'order_id' => $order->id,
                'seller_id' => $seller->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
            ]);

            // Create items for this fulfillment request
            foreach ($items as $orderItem) {
                FulfillmentRequestItem::create([
                    'fulfillment_request_id' => $fulfillmentRequest->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                    'price' => $orderItem->price,
                    'sku' => $orderItem->product->sku,
                ]);
            }

            $fulfillmentRequests->push($fulfillmentRequest);

            // Send notification to seller based on fulfillment method
            $this->notifySeller($fulfillmentRequest, $seller);
        }

        return $fulfillmentRequests;
    }

    /**
     * Notify seller about new fulfillment request
     */
    public function notifySeller(FulfillmentRequest $fulfillmentRequest, Seller $seller): void
    {
        // Mark as sent
        $fulfillmentRequest->update(['sent_at' => now()]);

        match ($seller->fulfillment_method) {
            'api' => $this->sendViaApi($fulfillmentRequest, $seller),
            'email' => $this->sendViaEmail($fulfillmentRequest, $seller),
            'csv' => $this->exportToCsv($fulfillmentRequest, $seller),
            default => $this->sendViaEmail($fulfillmentRequest, $seller),
        };
    }

    /**
     * Send fulfillment request via API
     */
    private function sendViaApi(FulfillmentRequest $fulfillmentRequest, Seller $seller): void
    {
        // Implementation for API integration
        // You can use Guzzle HTTP client here
        $payload = $this->prepareFulfillmentPayload($fulfillmentRequest);
        
        // Example: Send to seller's API endpoint
        // Http::post($seller->api_endpoint, $payload);
        
        \Log::info("Fulfillment request {$fulfillmentRequest->fulfillment_request_number} sent to seller {$seller->name} via API");
    }

    /**
     * Send fulfillment request via Email
     */
    private function sendViaEmail(FulfillmentRequest $fulfillmentRequest, Seller $seller): void
    {
        Mail::to($seller->email)->queue(new FulfillmentRequestNotification($fulfillmentRequest));
    }

    /**
     * Export fulfillment request to CSV
     */
    private function exportToCsv(FulfillmentRequest $fulfillmentRequest, Seller $seller): void
    {
        $payload = $this->prepareFulfillmentPayload($fulfillmentRequest);
        
        // Generate CSV file
        $filename = "fulfillment_{$fulfillmentRequest->fulfillment_request_number}.csv";
        $path = storage_path("app/fulfillment-exports/{$filename}");
        
        // Create directory if it doesn't exist
        if (!file_exists(storage_path('app/fulfillment-exports'))) {
            mkdir(storage_path('app/fulfillment-exports'), 0755, true);
        }

        // Write to CSV
        $file = fopen($path, 'w');
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
        
        \Log::info("Fulfillment request {$fulfillmentRequest->fulfillment_request_number} exported to CSV at {$path}");
    }

    /**
     * Prepare fulfillment payload
     */
    private function prepareFulfillmentPayload(FulfillmentRequest $fulfillmentRequest): array
    {
        return [
            'fulfillment_request_number' => $fulfillmentRequest->fulfillment_request_number,
            'order_number' => $fulfillmentRequest->order->order_number,
            'customer_name' => $fulfillmentRequest->order->customer_name,
            'customer_email' => $fulfillmentRequest->order->customer_email,
            'customer_phone' => $fulfillmentRequest->order->customer_phone,
            'shipping_address' => $fulfillmentRequest->order->shipping_address,
            'billing_address' => $fulfillmentRequest->order->billing_address,
            'items' => $fulfillmentRequest->items->map(function ($item) {
                return [
                    'sku' => $item->sku,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            })->toArray(),
            'total_amount' => $fulfillmentRequest->total_amount,
            'created_at' => $fulfillmentRequest->created_at,
        ];
    }

    /**
     * Confirm fulfillment request
     */
    public function confirmFulfillment(FulfillmentRequest $fulfillmentRequest): void
    {
        $fulfillmentRequest->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark fulfillment as shipped with tracking
     */
    public function markShipped(FulfillmentRequest $fulfillmentRequest, string $trackingNumber = null): void
    {
        $fulfillmentRequest->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
        ]);
    }

    /**
     * Get fulfillment status
     */
    public function getFulfillmentStatus(Order $order): array
    {
        $requests = $order->fulfillmentRequests;

        return [
            'total_requests' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'confirmed' => $requests->where('status', 'confirmed')->count(),
            'shipped' => $requests->where('status', 'shipped')->count(),
            'requests' => $requests->map(function ($request) {
                return [
                    'seller_name' => $request->seller->name,
                    'status' => $request->status,
                    'items_count' => $request->items->count(),
                    'total' => $request->total_amount,
                    'tracking_number' => $request->tracking_number,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get seller dashboard stats
     */
    public function getSellerStats(Seller $seller): array
    {
        $fulfillmentRequests = $seller->fulfillmentRequests();

        return [
            'total_orders' => $fulfillmentRequests->count(),
            'pending' => $fulfillmentRequests->clone()->pending()->count(),
            'confirmed' => $fulfillmentRequests->clone()->confirmed()->count(),
            'shipped' => $fulfillmentRequests->clone()->shipped()->count(),
            'total_revenue' => $fulfillmentRequests->sum('total_amount'),
            'last_30_days_revenue' => $fulfillmentRequests
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('total_amount'),
        ];
    }
}

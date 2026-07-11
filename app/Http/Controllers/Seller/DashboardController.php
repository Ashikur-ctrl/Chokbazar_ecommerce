<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentRequest;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $seller = auth()->user()->seller;

        $productsCount = Product::where('seller_id', $seller->id)->count();
        $activeProductsCount = Product::where('seller_id', $seller->id)->active()->count();
        $pendingOrders = FulfillmentRequest::where('seller_id', $seller->id)->pending()->count();
        $totalFulfilled = FulfillmentRequest::where('seller_id', $seller->id)->where('status', 'shipped')->count();
        $totalRevenue = FulfillmentRequest::where('seller_id', $seller->id)->whereIn('status', ['confirmed', 'shipped'])->sum('total_amount');

        $recentOrders = FulfillmentRequest::where('seller_id', $seller->id)
            ->with('order')
            ->latest()
            ->take(5)
            ->get();

        $recentProducts = Product::where('seller_id', $seller->id)
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'seller',
            'productsCount',
            'activeProductsCount',
            'pendingOrders',
            'totalFulfilled',
            'totalRevenue',
            'recentOrders',
            'recentProducts'
        ));
    }
}

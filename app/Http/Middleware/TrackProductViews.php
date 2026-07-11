<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackProductViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Track product views for recommendation system
        if ($request->route() && $request->route()->getName() === 'shop.product') {
            $productId = $request->route('product')?->id;

            if ($productId) {
                $userId = auth()->id();
                $sessionId = $request->session()->getId();

                // Record view behavior
                app(\App\Services\RecommendationService::class)->recordBehavior(
                    $userId,
                    $sessionId,
                    $productId,
                    'view',
                    ['source' => 'product_page', 'user_agent' => request()->userAgent()]
                );
            }
        }

        return $response;
    }
}

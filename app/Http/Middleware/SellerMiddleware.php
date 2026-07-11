<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('seller.login');
        }

        if (!auth()->user()->isSeller()) {
            abort(403, 'Unauthorized');
        }

        if (auth()->user()->seller?->is_suspended) {
            auth()->logout();
            return redirect()->route('seller.login')
                ->withErrors(['email' => 'Your seller account has been suspended. Contact admin for details.']);
        }

        return $next($request);
    }
}

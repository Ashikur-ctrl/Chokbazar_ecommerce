<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PageCacheMiddleware
{
    /**
     * Handle an incoming request — serve from cache if available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests for guests
        if (!$request->isMethod('GET') || auth()->check()) {
            return $next($request);
        }

        $key = 'page_cache:' . $request->fullUrl();
        $cached = cache()->get($key);

        if ($cached) {
            return response($cached['content'], 200, $cached['headers'] ?? []);
        }

        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful()) {
            cache()->put($key, [
                'content' => $response->getContent(),
                'headers' => ['Content-Type' => 'text/html; charset=UTF-8'],
            ], now()->addMinutes(30)); // Cache for 30 minutes
        }

        return $response;
    }

    /**
     * Invalidate page cache for URLs matching a pattern.
     */
    public static function invalidate(string $urlPattern = '*'): void
    {
        // With Redis, we could use SCAN + DEL with pattern matching.
        // With the database cache store, we use tagged cache or key prefix scanning.
        // For simplicity, flushing is done by clearing on model events.
        if (config('cache.default') === 'redis') {
            $prefix = config('cache.prefix', 'laravel') . ':page_cache:';
            // Redis SCAN approach would go here
        }
    }
}

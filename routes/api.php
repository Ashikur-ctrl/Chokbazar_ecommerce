<?php

use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public recommendation routes (throttled) — auth optional, fallback to session-based
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'personalized']);
    Route::get('/recommendations/personalized', [RecommendationController::class, 'personalized']);
    Route::get('/recommendations/similar/{productId}', [RecommendationController::class, 'productBased'])->where('productId', '[0-9]+');
    Route::get('/recommendations/frequently-bought/{productId}', [RecommendationController::class, 'frequentlyBoughtTogether'])->where('productId', '[0-9]+');
    Route::get('/recommendations/popular', [RecommendationController::class, 'popular']);
    Route::post('/recommendations/behavior', [RecommendationController::class, 'recordBehavior']);
});

// Public product filter API (rate limited)
Route::get('/products/filter', [ProductController::class, 'filter'])->middleware('throttle:30,1');

// Public location API for checkout
Route::get('/locations/districts', function () {
    return response()->json(
        \Illuminate\Support\Facades\DB::table('districts_upazilas')
            ->distinct()
            ->pluck('district')
            ->sort()
            ->values()
    );
});

Route::get('/locations/upazilas', function (\Illuminate\Http\Request $request) {
    $district = $request->query('district');
    if (!$district) {
        return response()->json([]);
    }
    return response()->json(
        \Illuminate\Support\Facades\DB::table('districts_upazilas')
            ->where('district', $district)
            ->pluck('upazila')
            ->sort()
            ->values()
    );
});

// Local sync endpoint - protected by API key
Route::post('/products/import', [ProductImportController::class, 'import'])
    ->middleware(['auth:sanctum', 'throttle:30,1']);
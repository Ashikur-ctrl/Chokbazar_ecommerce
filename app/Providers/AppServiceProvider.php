<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Query logging for local development
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    logger()->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                    ]);
                }
            });
        }

        // Invalidate page cache when products or categories are updated
        $clearCachedQueries = function () {
            cache()->forget('categories_active');
            cache()->forget('featured_products');
        };

        Product::saved($clearCachedQueries);
        Product::deleted($clearCachedQueries);
        Category::saved($clearCachedQueries);
        Category::deleted($clearCachedQueries);
    }
}

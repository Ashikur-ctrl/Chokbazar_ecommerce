<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Scheduled once a day (see routes/console.php below). Keeps a single cached
// rate so ConvertPriceJob never has to make a network call per product.
class RefreshFxRateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            // swap for whatever free/paid FX API you prefer
            $response = Http::timeout(10)->get('https://api.exchangerate-api.com/v4/latest/CNY');

            $rate = $response->json('rates.BDT');

            if ($rate) {
                Cache::put('fx_rate_cny_bdt', $rate, now()->addDay());
            } else {
                Log::warning('FX refresh returned no BDT rate, keeping previous cached value');
            }
        } catch (\Throwable $e) {
            Log::warning("FX rate refresh failed: {$e->getMessage()}");
            // deliberately not throwing — yesterday's cached rate is fine to keep using
        }
    }
}

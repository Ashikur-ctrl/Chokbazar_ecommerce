# Config additions

## 1. config/services.php — add this entry

```php
'import_1688' => [
    'api_key' => env('IMPORT_1688_API_KEY'),
    'base_url' => env('IMPORT_1688_BASE_URL', 'https://api.parse.bot/scraper/YOUR_ID'),
    'markup_percent' => env('IMPORT_1688_MARKUP_PERCENT', 25),
],
```

Add matching keys to `.env`:

```
IMPORT_1688_API_KEY=your-key-here
IMPORT_1688_BASE_URL=https://api.parse.bot/scraper/YOUR_ID
IMPORT_1688_MARKUP_PERCENT=25
```

## 2. app/Providers/AppServiceProvider.php — register the rate limiter

In `boot()`:

```php
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('source-1688-fetch', function ($job) {
    // tune to whatever your chosen provider's plan allows, e.g. 30/minute
    return Limit::perMinute(30);
});
```

## 3. routes/console.php — schedule the FX refresh + the two cron-driven processes

```php
use App\Jobs\RefreshFxRateJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RefreshFxRateJob)->dailyAt('01:00');
```

Your actual cPanel cron entries (already covered in the deployment plan):

```
* * * * * /opt/alt/php83/usr/bin/php /home/user/app/artisan schedule:run >> /home/user/schedule.log 2>&1
* * * * * (cd /home/user/app && /opt/alt/php83/usr/bin/php artisan queue:work --queue=imports --stop-when-empty --max-time=50 >> /home/user/queue-imports.log 2>&1)
* * * * * (cd /home/user/app && /opt/alt/php83/usr/bin/php artisan queue:work --queue=default --stop-when-empty --max-time=50 >> /home/user/queue-default.log 2>&1)
```

Two separate `queue:work` lines, one per queue, so a slow imports batch never
delays a customer's order-confirmation email. `--max-time=50` keeps each
worker invocation safely under a typical 60s shared-hosting execution cap.

## 4. Package needed

```
composer require intervention/image
```

Used in `DownloadProductImagesJob` for resizing images down before storage —
keeps disk usage sane when importing in bulk.

<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sitemap:generate')->dailyAt('03:00');

// Process queued emails (OrderConfirmation, FulfillmentRequestNotification) via cron-based queue worker
// Add the server-level cron job: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('queue:work --stop-when-empty --tries=3 --sleep=3')
    ->everyMinute()
    ->withoutOverlapping();

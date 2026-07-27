# 1688 Import Pipeline — Setup Guide

## Files in this package

```
database/migrations/2026_07_13_000001_create_import_tables.php
app/Models/ImportBatch.php
app/Models/ImportProduct.php
app/Jobs/FetchProductFromSourceJob.php
app/Jobs/TranslateProductJob.php
app/Jobs/DownloadProductImagesJob.php
app/Jobs/ConvertPriceJob.php
app/Jobs/RefreshFxRateJob.php
app/Filament/Resources/ImportProductResource.php
config-additions.md   ← services.php, .env, rate limiter, scheduler, composer package
```

## Install steps

1. Copy the migration into your project, run `php artisan migrate`.
2. Copy the two models, five jobs, and the Filament resource into matching folders.
3. Apply everything in `config-additions.md` — the `services.php` entry, `.env`
   keys, the rate limiter registration, and the `Schedule::job()` line.
4. `composer require intervention/image` (used for image resizing).
5. Generate the missing Filament page classes with:
   ```
   php artisan make:filament-resource ImportProduct --generate
   ```
   then keep the `table()` method from the resource file above — it has the
   approve/reject workflow already built in, so you mainly want the generated
   `Pages\ListImportProducts` / `Pages\ViewImportProduct` scaffolding.
6. Add the second `queue:work --queue=imports` cron line from
   `config-additions.md` alongside your existing default-queue cron.

## Starting an import batch

You'll want a small trigger — either a Filament page with a "paste offer IDs"
textarea, or an Artisan command. Simplest version, an Artisan command:

```php
// app/Console/Commands/Import1688Batch.php
public function handle()
{
    $offerIds = explode(',', $this->argument('offer_ids'));

    $batch = ImportBatch::create([
        'total_products' => count($offerIds),
        'created_by' => auth()->id(),
    ]);

    foreach ($offerIds as $offerId) {
        $item = ImportProduct::create([
            'import_batch_id' => $batch->id,
            'source_offer_id' => trim($offerId),
            'status' => 'pending',
        ]);

        FetchProductFromSourceJob::dispatch($item->id);
    }
}
```

Run with `php artisan import:1688 573787401272,573787401299,...` — 20-30 IDs
at a time to start, per the batch-sizing guidance from earlier.

## What still needs your judgment call

- **Which third-party 1688 API to subscribe to** — Parse.bot, TMAPI, and Apify
  all showed up in current search results with different pricing/endpoint
  shapes. `FetchProductFromSourceJob` assumes a `get_product_details`-style
  endpoint; adjust the response parsing to match whichever you pick.
- **Category/attribute mapping** — `sku_data` is stored as raw JSON; mapping
  1688's variant structure (color/size) into your own `Product`/`Variant`
  schema is business-specific and left as a TODO in the approve action.
- **Bangla translation** — the translate job currently does CN→EN only; if you
  want bilingual listings, extend the prompt to return `title_bn` too.

## Monitoring

Every job logs failures via `Log::warning` and writes `error_message` onto
the `import_products` row, so a stuck batch is diagnosable straight from the
Filament table — filter by `status` to see exactly where items are piling up.

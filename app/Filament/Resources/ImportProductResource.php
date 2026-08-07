<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportProductResource\Pages;
use App\Models\ImportProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ImportProductResource extends Resource
{
    protected static ?string $model = ImportProduct::class;

    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-arrow-down-tray'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Imports'; }

    public static function getNavigationLabel(): string { return 'Product Import Review Queue'; }

    protected static ?int $navigationSort = 99;

    // Only surface items that are actually ready for a human decision —
    // pending/fetched/translated/etc. are still mid-pipeline. Failed items are
    // included so reviewers can see and retry them.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['ready_for_review', 'approved', 'rejected', 'failed']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.0.local_path')
                    ->label('')
                    ->disk('public')
                    ->square(),

                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('batch.source')
                    ->label('Source')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'folder' => 'info',
                        'seller_ai' => 'success',
                        '1688' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('seller.company_name')
                    ->label('Seller')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price_cny')
                    ->label('CNY')
                    ->money('CNY'),

                Tables\Columns\TextColumn::make('price_bdt')
                    ->label('BDT (with markup)')
                    ->money('BDT')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'ready_for_review' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('batch.id')
                    ->label('Batch')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        '1688' => '1688',
                        'folder' => 'Folder',
                        'seller_ai' => 'Seller AI Upload',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('batch', fn (Builder $query) => $query->where('source', $data['value']))
                        : $query),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ready_for_review' => 'Ready for review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (ImportProduct $record) => $record->status === 'ready_for_review')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(fn () => Category::active()->pluck('name', 'id'))
                            ->default(fn () => Category::first()?->id)
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('price_bdt')
                            ->label('Price (BDT)')
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (ImportProduct $record) => $record->price_bdt)
                            ->required(),
                    ])
                    ->action(function (ImportProduct $record, array $data) {
                        static::approveImportProduct($record, $data);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (ImportProduct $record) => $record->status === 'ready_for_review')
                    ->requiresConfirmation()
                    ->action(fn (ImportProduct $record) => $record->update([
                        'status' => 'rejected',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ])),

                ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve selected')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('Category')
                                ->options(fn () => Category::active()->pluck('name', 'id'))
                                ->default(fn () => Category::first()?->id)
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function ($records, array $data) {
                            $firstCategory = $data['category_id'] ?? Category::first()?->id ?? 1;

                            foreach ($records as $record) {
                                if ($record->status !== 'ready_for_review') continue;

                                static::approveImportProduct($record, ['category_id' => $firstCategory]);
                            }
                        }),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Source Data')
                    ->columnSpan(1)
                    ->schema([
                        Text::make('batch.source')->label('Source')->badge(),
                        Text::make('source_offer_id')->label('Source ID'),
                        Text::make('title_cn')->label('Title (CN)'),
                        Text::make('description_cn')->label('Description (CN)')->html(),
                        Text::make('price_cny')->label('Price (CNY)'),
                    ]),

                Section::make('Translated Data')
                    ->columnSpan(1)
                    ->schema([
                        Text::make('title_en')->label('Title (EN)'),
                        Text::make('description_en')->label('Description (EN)')->html(),
                        Text::make('fx_rate_used')->label('FX Rate Used'),
                        Text::make('price_bdt')->label('Price (BDT with markup)'),
                        Text::make('sku_data.market_notes')->label('AI Market Notes'),
                        Text::make('seller.company_name')->label('Seller'),
                    ]),

                Section::make('Images')
                    ->columnSpanFull()
                    ->schema(function (ImportProduct $record): array {
                        $images = collect($record->images ?? []);
                        if ($images->isEmpty()) {
                            return [Text::make('No images available')];
                        }
                        return $images->map(fn ($img, $i) => Image::make(
                            url: asset('storage/' . ($img['local_path'] ?? '')),
                            alt: "Image $i"
                        )->imageHeight(200))->all();
                    }),

                Section::make('Status')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Text::make('status')->badge(),
                        Text::make('error_message')->label('Error')->visible(fn (?ImportProduct $record): bool => filled($record?->error_message)),
                        Text::make('reviewed_by')->label('Reviewed By'),
                        Text::make('reviewed_at')->label('Reviewed At')->dateTime(),
                        Text::make('created_at')->label('Imported At')->dateTime(),
                    ]),
            ]);
    }

    protected static function approveImportProduct(ImportProduct $record, array $data): void
    {
        $firstCategory = $data['category_id'] ?? Category::first()?->id ?? 1;
        $isFolderImport = in_array($record->batch?->source, ['folder', 'seller_ai'], true);
        $priceBdt = is_numeric($data['price_bdt'] ?? null) ? (float) $data['price_bdt'] : (float) ($record->price_bdt ?: 0);
        $fxRate = Cache::get('fx_rate_cny_bdt') ?? 7.2;

        $product = Product::create([
            'name' => $record->title_en ?: 'Imported Product',
            'description' => $record->description_en ?: ($record->title_en ?: 'Imported Product'),
            'price' => max(0, $priceBdt),
            'category_id' => $firstCategory,
            'seller_id' => $record->seller_id,
            'sourcing_type' => $isFolderImport ? 'local' : 'import',
            'fob_price_usd' => !$isFolderImport && $record->price_cny ? round($record->price_cny / $fxRate, 2) : null,
            'is_active' => true,
            'stock' => $isFolderImport ? 1 : 0,
            'tags' => $record->sku_data['tags'] ?? null,
        ]);

        if (!empty($record->images)) {
            foreach ($record->images as $index => $img) {
                $path = is_array($img) ? ($img['local_path'] ?? null) : $img;
                if ($path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'alt_text' => $product->name,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);

                    if ($index === 0) {
                        $product->update(['image' => $path]);
                    }
                }
            }
        }

        $record->update([
            'status' => 'approved',
            'product_id' => $product->id,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImportProducts::route('/'),
            'view' => Pages\ViewImportProduct::route('/{record}'),
        ];
    }
}

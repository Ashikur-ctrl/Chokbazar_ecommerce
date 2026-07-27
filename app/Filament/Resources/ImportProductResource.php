<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportProductResource\Pages;
use App\Models\ImportProduct;
use App\Models\Product;
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

class ImportProductResource extends Resource
{
    protected static ?string $model = ImportProduct::class;

    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-arrow-down-tray'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Imports'; }

    public static function getNavigationLabel(): string { return '1688 Review Queue'; }

    protected static ?int $navigationSort = 99;

    // Only surface items that are actually ready for a human decision —
    // pending/fetched/translated/etc. are still mid-pipeline.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['ready_for_review', 'approved', 'rejected']);
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
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('batch.id')
                    ->label('Batch')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ready_for_review' => 'Ready for review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (ImportProduct $record) => $record->status === 'ready_for_review')
                    ->requiresConfirmation()
                    ->action(function (ImportProduct $record) {
                        $product = Product::create([
                            'name' => $record->title_en,
                            'description' => $record->description_en,
                            'price' => $record->price_bdt,
                            'images' => collect($record->images)->pluck('local_path')->all(),
                            // map SKU/variant data into your Product/Variant schema here
                        ]);

                        $record->update([
                            'status' => 'approved',
                            'product_id' => $product->id,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
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
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status !== 'ready_for_review') continue;

                                $product = Product::create([
                                    'name' => $record->title_en,
                                    'description' => $record->description_en,
                                    'price' => $record->price_bdt,
                                    'images' => collect($record->images)->pluck('local_path')->all(),
                                ]);

                                $record->update([
                                    'status' => 'approved',
                                    'product_id' => $product->id,
                                    'reviewed_by' => auth()->id(),
                                    'reviewed_at' => now(),
                                ]);
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
                        Text::make('source_offer_id')->label('1688 Offer ID'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImportProducts::route('/'),
            'view' => Pages\ViewImportProduct::route('/{record}'),
        ];
    }
}

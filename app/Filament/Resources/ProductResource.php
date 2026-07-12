<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-shopping-bag'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Catalogue'; }


    protected static ?string $model = Product::class;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, ignoreRecord: true),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('short_description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('৳'),
                        Forms\Components\TextInput::make('sale_price')
                            ->numeric()
                            ->prefix('৳')
                            ->nullable(),
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('moq')
                            ->numeric()
                            ->default(1)
                            ->label('Min Order Quantity'),
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->numeric()
                            ->default(5),
                        Forms\Components\TextInput::make('sku')
                            ->maxLength(255)
                            ->unique(Product::class, ignoreRecord: true)
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->visibility('public'),
                    ]),

                Forms\Components\Section::make('Status & Visibility')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('is_wholesale')
                            ->label('Wholesale'),
                        Forms\Components\Toggle::make('is_b2b_only')
                            ->label('B2B Only'),
                        Forms\Components\Select::make('visibility_status')
                            ->options([
                                'active' => 'Active',
                                'draft' => 'Draft',
                                'archived' => 'Archived',
                            ])
                            ->default('active'),
                    ])->columns(3),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\Textarea::make('seo_description')
                            ->maxLength(65535)
                            ->nullable(),
                        Forms\Components\Textarea::make('tags')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Price')
                    ->money('BDT')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('price', $direction)),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Product $record): string => $record->stock <= $record->low_stock_threshold ? 'danger' : 'success')
                    ->badge()
                    ->formatStateUsing(fn ($state, Product $record) => $state <= $record->low_stock_threshold ? "{$state} (Low)" : $state),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('seller')
                    ->relationship('seller', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\SelectFilter::make('visibility_status')
                    ->options([
                        'active' => 'Active',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggleActive')
                        ->label('Toggle Active')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => !$record->is_active]))),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}

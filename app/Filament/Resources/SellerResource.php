<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerResource\Pages;
use App\Models\Seller;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SellerResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-store'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Marketplace'; }


    protected static ?string $model = Seller::class;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Business Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Seller::class, ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('sellers/logos')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->directory('sellers/covers')
                            ->visibility('public'),
                    ]),

                Forms\Components\Section::make('Status & Verification')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Select::make('verification_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                        Forms\Components\TextInput::make('commission_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->nullable(),
                    ])->columns(3),

                Forms\Components\Section::make('Business Details')
                    ->schema([
                        Forms\Components\Select::make('business_type')
                            ->options([
                                'individual' => 'Individual',
                                'company' => 'Company',
                                'partnership' => 'Partnership',
                            ])->nullable(),
                        Forms\Components\TextInput::make('year_established')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('website_url')
                            ->url()
                            ->nullable(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('address'),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('state'),
                        Forms\Components\TextInput::make('postal_code'),
                        Forms\Components\TextInput::make('country'),
                    ])->columns(2),

                Forms\Components\Section::make('Shipping & Returns')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_days_min')
                            ->numeric()
                            ->nullable()
                            ->label('Min Shipping Days'),
                        Forms\Components\TextInput::make('shipping_days_max')
                            ->numeric()
                            ->nullable()
                            ->label('Max Shipping Days'),
                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->numeric()
                            ->prefix('৳')
                            ->nullable(),
                        Forms\Components\Textarea::make('return_policy')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('verification_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_products')
                    ->label('Products')
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_percentage')
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            \App\Filament\Resources\SellerResource\RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
            'view' => Pages\ViewSeller::route('/{record}'),
        ];
    }
}

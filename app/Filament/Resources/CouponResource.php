<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-ticket'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Marketing'; }


    protected static ?string $model = Coupon::class;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->unique(Coupon::class, ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->prefix('Value'),
                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->numeric()
                            ->prefix('৳')
                            ->nullable()
                            ->label('Min Order Amount'),
                    ])->columns(2),

                Forms\Components\Section::make('Usage')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->default(0)
                            ->label('Usage Limit (0 = unlimited)'),
                        Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Validity')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->required(),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'percentage' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($state, Coupon $record) => $record->type === 'percentage' ? "{$state}%" : "৳{$state}"),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Usage Limit'),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-truck'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Sales'; }


    protected static ?string $model = Order::class;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('invoice_number')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('customer_name')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('customer_email')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('customer_phone')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'returned' => 'Returned',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Shipping')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tracking_number')
                            ->nullable(),
                        Forms\Components\TextInput::make('courier_name')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('৳'),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('৳'),
                        Forms\Components\TextInput::make('tax_amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('৳'),
                        Forms\Components\TextInput::make('discount_amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('৳'),
                        Forms\Components\TextInput::make('total_amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('৳'),
                    ])->columns(3),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'returned' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Ordered At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'returned' => 'Returned',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Order $record) => route('admin-legacy.orders.invoice', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}

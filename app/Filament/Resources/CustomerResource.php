<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-users'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Sales'; }


    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                        Forms\Components\TextInput::make('email_verified_at')
                            ->label('Email Verified')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Not Verified'),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Customer Since')
                            ->disabled()
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Spent')
                    ->money('BDT')
                    ->sortable()
                    ->getStateUsing(fn (User $record): float => (float) $record->orders()->where('status', 'delivered')->sum('total_amount')),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verified')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Joined'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}

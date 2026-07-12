<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order Items';

    public function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('৳'),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('৳'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('price')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total')
                    ->money('BDT'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-megaphone'; }

    public static function getNavigationGroup(): string | \UnitEnum | null { return 'Content'; }


    protected static ?string $model = Promotion::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promotion Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('banner_image')
                            ->image()
                            ->directory('promotions')
                            ->visibility('public'),
                    ]),

                Forms\Components\Section::make('Type & Value')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'percentage' => 'Percentage Off',
                                'fixed' => 'Fixed Amount Off',
                                'announcement' => 'Announcement',
                            ])
                            ->default('announcement'),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->prefix('Value')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Schedule')
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        'announcement' => 'warning',
                        default => 'gray',
                    }),
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
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage Off',
                        'fixed' => 'Fixed Amount Off',
                        'announcement' => 'Announcement',
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
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}

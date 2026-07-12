<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $recordTitleAttribute = 'description';

    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): string | \UnitEnum | null { return 'System'; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Resource')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Resource Type')
                    ->options(fn () => Activity::query()
                        ->distinct('subject_type')
                        ->pluck('subject_type', 'subject_type')
                        ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                        ->toArray()),
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ImportProductResource\Pages;

use App\Filament\Resources\ImportProductResource;
use App\Models\ImportProduct;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewImportProduct extends ViewRecord
{
    protected static string $resource = ImportProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn (ImportProduct $record) => $record->status === 'ready_for_review')
                ->requiresConfirmation()
                ->action(function (ImportProduct $record): void {
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

                    Notification::make()->success()->title('Product approved and created')->send();
                }),

            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn (ImportProduct $record) => $record->status === 'ready_for_review')
                ->requiresConfirmation()
                ->action(function (ImportProduct $record): void {
                    $record->update([
                        'status' => 'rejected',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                    Notification::make()->danger()->title('Product rejected')->send();
                }),
        ];
    }
}

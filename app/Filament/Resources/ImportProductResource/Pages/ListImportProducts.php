<?php

namespace App\Filament\Resources\ImportProductResource\Pages;

use App\Filament\Resources\ImportProductResource;
use App\Models\ImportBatch;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListImportProducts extends ListRecords
{
    protected static string $resource = ImportProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newImport')
                ->label('New Import')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->form([
                    Textarea::make('offer_ids')
                        ->label('1688 Offer IDs')
                        ->placeholder('Paste comma-separated 1688 offer IDs')
                        ->rows(4)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $ids = array_map('trim', explode(',', $data['offer_ids']));
                    $ids = array_filter($ids);

                    if (empty($ids)) {
                        Notification::make()->danger()->title('No valid IDs provided')->send();
                        return;
                    }

                    $batch = ImportBatch::create([
                        'source' => '1688',
                        'status' => 'pending',
                        'total_products' => count($ids),
                        'created_by' => auth()->id(),
                    ]);

                    foreach ($ids as $offerId) {
                        $batch->products()->create([
                            'source_offer_id' => $offerId,
                            'status' => 'pending',
                        ]);
                    }

                    Notification::make()
                        ->success()
                        ->title("Batch #{$batch->id} created — {$batch->total_products} products")
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'ready_for_review' => Tab::make('Ready for Review')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'ready_for_review')),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'approved')),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'rejected')),
        ];
    }
}

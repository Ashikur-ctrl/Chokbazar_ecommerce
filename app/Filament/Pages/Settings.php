<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    public static function getNavigationIcon(): string | \BackedEnum | null { return 'heroicon-o-cog-6-tooth'; }
    public static function getNavigationGroup(): string | \UnitEnum | null { return 'System'; }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'shipping_rate' => Setting::get('shipping_rate', '60'),
            'tax_rate' => Setting::get('tax_rate', '0'),
            'currency_symbol' => Setting::get('currency_symbol', '৳'),
            'store_name' => Setting::get('store_name', 'Chokbazar'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Store Settings')
                    ->schema([
                        Forms\Components\TextInput::make('store_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('currency_symbol')
                            ->required()
                            ->maxLength(10)
                            ->default('৳'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_rate')
                            ->required()
                            ->numeric()
                            ->prefix('৳')
                            ->label('Default Shipping Rate'),
                        Forms\Components\TextInput::make('tax_rate')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->label('Tax Rate'),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Gateways')
                    ->description('Configured in .env — shown here for reference only')
                    ->schema([
                        Forms\Components\Placeholder::make('sslcommerz_status')
                            ->label('SSLCommerz')
                            ->content(fn () => env('SSLC_STORE_ID') ? '✅ Configured' : '❌ Not configured'),
                        Forms\Components\Placeholder::make('bkash_status')
                            ->label('bKash')
                            ->content(fn () => env('BKASH_MERCHANT_NUMBER') ? '✅ Configured' : '❌ Not configured'),
                        Forms\Components\Placeholder::make('nagad_status')
                            ->label('Nagad')
                            ->content(fn () => env('NAGAD_MERCHANT_NUMBER') ? '✅ Configured' : '❌ Not configured'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }
}

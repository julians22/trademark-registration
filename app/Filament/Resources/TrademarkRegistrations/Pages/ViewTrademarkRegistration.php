<?php

namespace App\Filament\Resources\TrademarkRegistrations\Pages;

use App\Filament\Resources\TrademarkRegistrations\TrademarkRegistrationResource;
use App\Services\TrademarkRegistrationPdfService;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTrademarkRegistration extends ViewRecord
{
    protected static string $resource = TrademarkRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('input_pricing')
                ->label('Input Harga')
                ->icon('heroicon-o-currency-dollar')
                ->form([
                    TextInput::make('base_price')
                        ->label('Harga Dasar (IDR)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->default(fn (): float => (float) ($this->getRecord()->base_price ?? 0)),
                    Repeater::make('pricing_rows')
                        ->label('Harga per Class')
                        ->schema([
                            TextInput::make('class_name')
                                ->label('Class')
                                ->disabled()
                                ->dehydrated(),
                            TextInput::make('price')
                                ->label('Harga (IDR)')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->prefix('Rp'),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->default(fn (): array => $this->buildPricingRows()),
                ])
                ->action(function (array $data): void {
                    $pricingMap = $this->buildPricingMap($data['pricing_rows'] ?? []);
                    $basePrice = (float) ($data['base_price'] ?? 0);

                    if ($pricingMap === []) {
                        Notification::make()
                            ->title('Tidak ada harga yang disimpan.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->getRecord()->update([
                        'class_pricing' => $pricingMap,
                        'base_price' => $basePrice,
                        'total_price' => $basePrice + array_sum($pricingMap),
                        'pricing_completed_at' => now(),
                    ]);

                    $pdfPath = app(TrademarkRegistrationPdfService::class)
                        ->generateAndStore($this->getRecord()->refresh());

                    $this->getRecord()->update([
                        'pdf_path' => $pdfPath,
                        'pdf_generated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Harga dan PDF berhasil disimpan.')
                        ->success()
                        ->send();
                }),
            Action::make('print_pdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->visible(fn (): bool => $this->hasCompletePricing() && filled($this->getRecord()->pdf_path))
                ->url(fn (): ?string => $this->getRecord()->temporaryPdfUrl())
                ->openUrlInNewTab(),
            Action::make('regenerate_pdf')
                ->label('Regenerate PDF')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn (): bool => $this->hasCompletePricing())
                ->action(function (): void {
                    $pdfPath = app(TrademarkRegistrationPdfService::class)
                        ->generateAndStore($this->getRecord()->refresh());

                    $this->getRecord()->update([
                        'pdf_path' => $pdfPath,
                        'pdf_generated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('PDF berhasil di-generate ulang.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function buildPricingRows(): array
    {
        $classes = is_array($this->getRecord()->selected_classes) ? $this->getRecord()->selected_classes : [];
        $existing = is_array($this->getRecord()->class_pricing) ? $this->getRecord()->class_pricing : [];

        return collect($classes)
            ->map(fn (string $className): array => [
                'class_name' => $className,
                'price' => $existing[$className] ?? null,
            ])
            ->values()
            ->all();
    }

    protected function buildPricingMap(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $className = (string) ($row['class_name'] ?? '');

            if ($className === '') {
                continue;
            }

            $result[$className] = (float) ($row['price'] ?? 0);
        }

        return $result;
    }

    protected function hasCompletePricing(): bool
    {
        $classes = is_array($this->getRecord()->selected_classes) ? $this->getRecord()->selected_classes : [];
        $pricing = is_array($this->getRecord()->class_pricing) ? $this->getRecord()->class_pricing : [];

        if ($classes === []) {
            return false;
        }

        foreach ($classes as $className) {
            if (! array_key_exists($className, $pricing)) {
                return false;
            }
        }

        return true;
    }
}

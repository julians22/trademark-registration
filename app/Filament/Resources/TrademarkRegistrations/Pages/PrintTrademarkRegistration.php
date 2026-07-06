<?php

namespace App\Filament\Resources\TrademarkRegistrations\Pages;

use App\Filament\Resources\TrademarkRegistrations\TrademarkRegistrationResource;
use App\Services\TrademarkRegistrationPdfService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class PrintTrademarkRegistration extends ViewRecord
{
    protected static string $resource = TrademarkRegistrationResource::class;

    protected string $view = 'filament.resources.trademark-registrations.pages.print-trademark-registration';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Detail')
                ->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getRecord()])),
            Action::make('print_pdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->color('primary')
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

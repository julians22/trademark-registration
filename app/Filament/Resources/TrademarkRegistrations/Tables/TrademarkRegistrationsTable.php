<?php

namespace App\Filament\Resources\TrademarkRegistrations\Tables;

use App\Filament\Resources\TrademarkRegistrations\TrademarkRegistrationResource;
use App\Services\TrademarkRegistrationPdfService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrademarkRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trademark_name')
                    ->label('Trademark')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('trademark_type')
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('filing_type')
                    ->badge()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('applicant_name')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('applicant_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('selected_classes')
                ->toggleable()
                    ->label('Classes')
                    ->getStateUsing(fn ($record): string => self::formatSelectedClasses($record))
                    ->limit(40),
                TextColumn::make('selected_countries')
                    ->label('Countries')
                    ->getStateUsing(fn ($record): string => self::formatSelectedCountries($record))
                    ->limit(40),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('base_price')
                    ->label('Base Price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
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
                            ->default(fn ($record): float => (float) ($record->base_price ?? 0)),
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
                            ->default(fn ($record): array => self::buildPricingRows($record)),
                    ])
                    ->action(function ($record, array $data): void {
                        $pricingMap = self::buildPricingMap($data['pricing_rows'] ?? []);
                        $basePrice = (float) ($data['base_price'] ?? 0);

                        if ($pricingMap === []) {
                            Notification::make()
                                ->title('Tidak ada harga yang disimpan.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->update([
                            'class_pricing' => $pricingMap,
                            'base_price' => $basePrice,
                            'total_price' => $basePrice + array_sum($pricingMap),
                            'pricing_completed_at' => now(),
                        ]);

                        $pdfPath = app(TrademarkRegistrationPdfService::class)->generateAndStore($record->refresh());

                        $record->update([
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
                    ->visible(fn ($record): bool => self::hasCompletePricing($record) && filled($record->pdf_path))
                    ->url(fn ($record): ?string => $record->temporaryPdfUrl())
                    ->openUrlInNewTab(),
                Action::make('regenerate_pdf')
                    ->label('Regenerate PDF')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record): bool => self::hasCompletePricing($record))
                    ->action(function ($record): void {
                        $pdfPath = app(TrademarkRegistrationPdfService::class)->generateAndStore($record->refresh());

                        $record->update([
                            'pdf_path' => $pdfPath,
                            'pdf_generated_at' => now(),
                        ]);

                        Notification::make()
                            ->title('PDF berhasil di-generate ulang.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected static function buildPricingRows($record): array
    {
        $classes = self::extractArray($record->selected_classes);

        if ($classes === [] && is_array($record->class_pricing)) {
            $classes = array_keys($record->class_pricing);
        }

        $existing = is_array($record->class_pricing) ? $record->class_pricing : [];

        return collect($classes)
            ->map(fn (string $className): array => [
                'class_name' => $className,
                'price' => $existing[$className] ?? null,
            ])
            ->values()
            ->all();
    }

    protected static function buildPricingMap(array $rows): array
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

    protected static function hasCompletePricing($record): bool
    {
        $classes = self::extractArray($record->selected_classes);

        if ($classes === [] && is_array($record->class_pricing)) {
            $classes = array_keys($record->class_pricing);
        }

        $pricing = is_array($record->class_pricing) ? $record->class_pricing : [];

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

    protected static function formatSelectedClasses($record): string
    {
        $classes = self::extractArray($record->selected_classes);

        if ($classes === [] && is_array($record->class_pricing)) {
            $classes = array_keys($record->class_pricing);
        }

        return $classes !== [] ? implode(', ', $classes) : '-';
    }

    protected static function formatSelectedCountries($record): string
    {
        $countries = self::extractArray($record->selected_countries);

        return $countries !== [] ? implode(', ', $countries) : '-';
    }

    protected static function extractArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}

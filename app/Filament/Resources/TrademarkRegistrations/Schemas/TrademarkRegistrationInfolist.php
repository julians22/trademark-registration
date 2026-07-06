<?php

namespace App\Filament\Resources\TrademarkRegistrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

class TrademarkRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant Information')
                    ->schema([
                        TextEntry::make('applicant_name')
                            ->placeholder('-'),
                        TextEntry::make('applicant_company')
                            ->placeholder('-'),
                        TextEntry::make('applicant_email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('active_phone_number')
                            ->placeholder('-'),
                        TextEntry::make('whatsapp_number')
                            ->placeholder('-'),
                        TextEntry::make('wechat_number')
                            ->placeholder('-'),
                    ]),
                Section::make('Trademark Information')
                    ->schema([
                        TextEntry::make('trademark_name')
                            ->placeholder('-'),
                        TextEntry::make('trademark_type')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('filing_type')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('selected_classes')
                            ->label('Selected Classes')
                            ->getStateUsing(fn ($record): string => self::formatSelectedClasses($record))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('selected_countries')
                            ->label('Selected Countries')
                            ->getStateUsing(fn ($record): string => self::formatSelectedCountries($record))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Uploaded Files')
                    ->schema([
                        TextEntry::make('file_paths')
                            ->formatStateUsing(
                                fn (mixed $state): string => is_array($state)
                                    ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                    : '-'
                            )
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('uploaded_preview')
                            ->label('File Preview')
                            ->getStateUsing(fn ($record): string => self::buildUploadPreviewHtml($record))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('class_pricing')
                            ->label('Class Pricing')
                            ->formatStateUsing(
                                fn (mixed $state): string => is_array($state)
                                    ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                    : '-'
                            )
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('base_price')
                            ->money('IDR')
                            ->placeholder('-'),
                        TextEntry::make('total_price')
                            ->money('IDR')
                            ->placeholder('-'),
                        TextEntry::make('pricing_completed_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('pdf_path')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('pdf_generated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }

    protected static function formatSelectedClasses($record): string
    {
        $classes = [];

        if (is_array($record->selected_classes)) {
            $classes = $record->selected_classes;
        } elseif (is_string($record->selected_classes) && $record->selected_classes !== '') {
            $decoded = json_decode($record->selected_classes, true);
            $classes = is_array($decoded) ? $decoded : [];
        }

        if ($classes === [] && is_array($record->class_pricing)) {
            $classes = array_keys($record->class_pricing);
        }

        return $classes !== [] ? implode(', ', $classes) : '-';
    }

    protected static function formatSelectedCountries($record): string
    {
        $countries = [];

        if (is_array($record->selected_countries)) {
            $countries = $record->selected_countries;
        } elseif (is_string($record->selected_countries) && $record->selected_countries !== '') {
            $decoded = json_decode($record->selected_countries, true);
            $countries = is_array($decoded) ? $decoded : [];
        }

        return $countries !== [] ? implode(', ', $countries) : '-';
    }

    protected static function buildUploadPreviewHtml($record): string
    {
        $storedFiles = self::collectStoredFiles($record->file_paths);

        if ($storedFiles === []) {
            return '<span style="color:#6b7280;">No uploaded file.</span>';
        }

        $imageItems = [];
        $hasMusicAttachment = false;
        $hasPdfAttachment = false;

        foreach ($storedFiles as $path) {
            if (self::isImagePath($path)) {
                $imageItems[] = sprintf(
                    '<div style="border:1px solid #e5e7eb;border-radius:10px;padding:8px;background:#fff;">'
                    .'<img src="%s" alt="upload" style="width:100%%;max-height:180px;object-fit:contain;border-radius:6px;" />'
                    .'</div>',
                    e(route('storage.local', ['path' => $path])),
                );

                continue;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
                $hasMusicAttachment = true;
            }

            if ($extension === 'pdf') {
                $hasPdfAttachment = true;
            }
        }

        $html = '<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">';
        $html .= implode('', $imageItems);
        $html .= '</div>';

        $labels = [];

        if ($hasMusicAttachment) {
            $labels[] = 'Lampiran File musik';
        }

        if ($hasPdfAttachment) {
            $labels[] = 'Lampiran File PDF';
        }

        if ($labels !== []) {
            $chips = implode('', array_map(
                fn (string $label): string => '<span style="display:inline-block;margin:6px 6px 0 0;padding:4px 8px;border-radius:999px;background:#f3f4f6;color:#1f2937;font-size:12px;">'.e($label).'</span>',
                $labels,
            ));

            $html .= '<div style="margin-top:8px;">'.$chips.'</div>';
        }

        return $html;
    }

    protected static function collectStoredFiles(mixed $filePaths): array
    {
        if (! is_array($filePaths)) {
            return [];
        }

        return collect(Arr::flatten($filePaths))
            ->filter(fn (mixed $value): bool => is_string($value) && $value !== '')
            ->values()
            ->all();
    }

    protected static function isImagePath(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}

<?php

namespace App\Services;

use App\Models\TrademarkRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class TrademarkRegistrationPdfService
{
    public function generateAndStore(TrademarkRegistration $registration): string
    {
        $record = $registration->fresh();
        $storedFiles = $this->collectStoredFiles($record->file_paths);
        $imageFiles = $this->buildImageFiles($storedFiles);
        $nonImageFiles = array_values(array_filter(
            $storedFiles,
            fn (string $path): bool => ! $this->isImagePath($path),
        ));

        $pdf = Pdf::loadView('pdf.trademark-registration-invoice', [
            'record' => $record,
            'printedAt' => now(),
            'imageFiles' => $imageFiles,
            'nonImageFiles' => $nonImageFiles,
        ])->setPaper('a4');

        $filename = sprintf('invoice-%s.pdf', now()->format('YmdHis'));
        $path = sprintf('trademark-pdfs/%d/%s', $registration->id, $filename);

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    protected function collectStoredFiles(mixed $filePaths): array
    {
        if (! is_array($filePaths)) {
            return [];
        }

        return collect(Arr::flatten($filePaths))
            ->filter(fn (mixed $value): bool => is_string($value) && $value !== '')
            ->values()
            ->all();
    }

    protected function buildImageFiles(array $storedFiles): array
    {
        $result = [];

        foreach ($storedFiles as $path) {
            if (! $this->isImagePath($path)) {
                continue;
            }

            if (! Storage::disk('local')->exists($path)) {
                continue;
            }

            $absolutePath = Storage::disk('local')->path($path);
            $mimeType = mime_content_type($absolutePath) ?: 'image/jpeg';
            $binary = file_get_contents($absolutePath);

            if ($binary === false) {
                continue;
            }

            $result[] = [
                'path' => $path,
                'data_uri' => 'data:'.$mimeType.';base64,'.base64_encode($binary),
            ];
        }

        return $result;
    }

    protected function isImagePath(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}

<x-filament-panels::page>
    @php
        $record = $this->getRecord();
        $pdfUrl = $record->temporaryPdfUrl();
    @endphp

    <div class="space-y-4" x-data x-on:print-page.window="window.print()">
        @if (! $pdfUrl)
            <div class="alert alert-warning">
                <span>PDF belum tersedia. Silakan input harga per class terlebih dahulu dari halaman detail.</span>
            </div>
        @else
            <div class="alert alert-info">
                <span>PDF sudah disimpan di storage dan siap dicetak.</span>
            </div>

            <div class="border rounded-xl overflow-hidden" style="height: 78vh;">
                <iframe src="{{ $pdfUrl }}" class="w-full h-full" title="Trademark PDF Preview"></iframe>
            </div>
        @endif
    </div>
</x-filament-panels::page>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Trademark Registration Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .sheet {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 16px;
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .subtitle {
            margin-top: 4px;
            color: #4b5563;
            font-size: 10px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .meta td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            width: 33.33%;
        }

        .meta .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            display: block;
        }

        .meta .value {
            margin-top: 2px;
            font-size: 11px;
            font-weight: 700;
            display: block;
        }

        .section-title {
            margin: 12px 0 6px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #1f2937;
        }

        .detail {
            width: 100%;
            border-collapse: collapse;
        }

        .detail td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: top;
        }

        .detail .key {
            width: 180px;
            font-weight: 600;
            color: #374151;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: top;
        }

        .items th {
            background: #f3f4f6;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            width: 280px;
            margin-left: auto;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
        }

        .summary .total {
            background: #111827;
            color: #ffffff;
            font-weight: 700;
        }

        .files {
            margin-top: 8px;
            border: 1px solid #e5e7eb;
            padding: 8px;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 10px;
        }

        .image-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .image-grid td {
            width: 50%;
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }

        .image-grid img {
            display: block;
            width: 100%;
            max-height: 180px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            margin-bottom: 6px;
        }

        .image-caption {
            font-size: 9px;
            color: #6b7280;
            word-break: break-word;
        }

        .footer {
            margin-top: 12px;
            border-top: 1px dashed #d1d5db;
            padding-top: 8px;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
@php
    $invoiceNumber = 'TRM-' . str_pad((string) $record->id, 6, '0', STR_PAD_LEFT);
    $issuedAt = optional($record->created_at)->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i');
    $classPricing = is_array($record->class_pricing) ? $record->class_pricing : [];
    $classes = is_array($record->selected_classes) ? $record->selected_classes : [];
    $countries = is_array($record->selected_countries) ? $record->selected_countries : [];
    $classCount = count($classes);
    $countryCount = count($countries);
    $basePrice = (float) ($record->base_price ?? 0);
    $totalPrice = (float) ($record->total_price ?? 0);
    $imageFiles = $imageFiles ?? [];
    $nonImageFiles = $nonImageFiles ?? [];
    $hasMusicAttachment = collect($nonImageFiles)->contains(function (string $path): bool {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'mp4', 'mov', 'avi', 'mkv', 'webm'], true);
    });
    $hasPdfAttachment = collect($nonImageFiles)->contains(function (string $path): bool {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $extension === 'pdf';
    });
    $description = sprintf(
        'Jenis Trademark: %s | Type: %s | Filing: %s | Negara: %s',
        $record->trademark_name ?: '-',
        strtoupper((string) $record->trademark_type),
        strtoupper((string) $record->filing_type),
        $countries !== [] ? implode(', ', $countries) : '-',
    );
@endphp

<div class="sheet">
    <div class="header">
        <h1 class="title">Trademark Registration Invoice</h1>
        <div class="subtitle">System generated document for trademark submission</div>
    </div>

    <table class="meta">
        <tr>
            <td>
                <span class="label">Invoice No</span>
                <span class="value">{{ $invoiceNumber }}</span>
            </td>
            <td>
                <span class="label">Issue Date</span>
                <span class="value">{{ $issuedAt }}</span>
            </td>
            <td>
                <span class="label">Registration ID</span>
                <span class="value">#{{ $record->id }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Applicant Information</div>
    <table class="detail">
        <tr><td class="key">Name</td><td>{{ $record->applicant_name ?: '-' }}</td></tr>
        <tr><td class="key">Company</td><td>{{ $record->applicant_company ?: '-' }}</td></tr>
        <tr><td class="key">Email</td><td>{{ $record->applicant_email ?: '-' }}</td></tr>
        <tr><td class="key">Active Phone Number</td><td>{{ $record->active_phone_number ?: '-' }}</td></tr>
        <tr><td class="key">Whatsapp Number</td><td>{{ $record->whatsapp_number ?: '-' }}</td></tr>
        <tr><td class="key">WeChat Number</td><td>{{ $record->wechat_number ?: '-' }}</td></tr>
    </table>

    <div class="section-title">Invoice Items</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 46px;">No</th>
                <th>Description</th>
                <th style="width: 70px;">Qty</th>
                <th style="width: 120px;">Unit Price</th>
                <th style="width: 120px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $index => $className)
                @php
                    $price = (float) ($classPricing[$className] ?? 0);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $className }}
                        @if($index === 0)
                            <div style="margin-top: 3px; color: #6b7280; font-size: 10px;">
                                Trademark: {{ $record->trademark_name ?: '-' }} | Type: {{ strtoupper((string) $record->trademark_type) }} | Filing: {{ strtoupper((string) $record->filing_type) }}
                            </div>
                        @endif
                    </td>
                    <td>1</td>
                    <td class="text-right">Rp {{ number_format($price, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td>No class selected</td>
                    <td>1</td>
                    <td class="text-right">Rp 0,00</td>
                    <td class="text-right">Rp 0,00</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Base Price</td>
            <td class="text-right">Rp {{ number_format($basePrice, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Selected Classes</td>
            <td class="text-right">{{ $classCount }}</td>
        </tr>
        <tr>
            <td>Selected Countries</td>
            <td class="text-right">{{ $countryCount }}</td>
        </tr>
        <tr class="total">
            <td>Total</td>
            <td class="text-right">Rp {{ number_format($totalPrice, 2, ',', '.') }}</td>
        </tr>
    </table>

    <div class="section-title">Selected Countries</div>
    <table class="detail">
        <tr>
            <td>{{ $countries !== [] ? implode(', ', $countries) : '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Deskripsi</div>
    <table class="detail">
        <tr>
            <td>{{ $description }}</td>
        </tr>
    </table>

    <div class="section-title">Uploaded Image Files</div>
    @if ($imageFiles !== [])
        <table class="image-grid">
            <tr>
                @foreach ($imageFiles as $index => $imageFile)
                    @if ($index > 0 && $index % 2 === 0)
                        </tr><tr>
                    @endif
                    <td>
                        <img src="{{ $imageFile['data_uri'] }}" alt="Uploaded image {{ $index + 1 }}">
                        <div class="image-caption">Lampiran Gambar</div>
                    </td>
                @endforeach
                @if (count($imageFiles) % 2 !== 0)
                    <td></td>
                @endif
            </tr>
        </table>
    @else
        <table class="detail">
            <tr>
                <td>No image file uploaded.</td>
            </tr>
        </table>
    @endif

    <div class="section-title">Attachment Notes</div>
    <table class="detail">
        <tr>
            <td>
                @if ($hasMusicAttachment)
                    Lampiran File musik
                @endif

                @if ($hasMusicAttachment && $hasPdfAttachment)
                    <br>
                @endif

                @if ($hasPdfAttachment)
                    Lampiran File PDF
                @endif

                @if (! $hasMusicAttachment && ! $hasPdfAttachment)
                    Tidak ada lampiran non-gambar.
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Printed at {{ $printedAt->format('Y-m-d H:i') }}
    </div>
</div>
</body>
</html>

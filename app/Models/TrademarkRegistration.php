<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class TrademarkRegistration extends Model
{
    protected $fillable = [
        'applicant_type',
        'applicant_name',
        'applicant_company',
        'applicant_email',
        'active_phone_number',
        'whatsapp_number',
        'wechat_number',
        'trademark_name',
        'trademark_type',
        'filing_type',
        'selected_classes',
        'class_pricing',
        'base_price',
        'total_price',
        'pricing_completed_at',
        'selected_countries',
        'file_paths',
        'pdf_path',
        'pdf_generated_at',
        'notes',
    ];

    protected $casts = [
        'selected_classes' => 'array',
        'class_pricing' => 'array',
        'base_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'pricing_completed_at' => 'datetime',
        'selected_countries' => 'array',
        'file_paths' => 'array',
        'pdf_generated_at' => 'datetime',
    ];

    public function temporaryPdfUrl(int $minutes = 15, bool $download = false): ?string
    {
        if (blank($this->pdf_path)) {
            return null;
        }

        return URL::temporarySignedRoute(
            'private.trademark.file',
            now()->addMinutes($minutes),
            [
                'path' => $this->pdf_path,
                'download' => $download ? 1 : 0,
            ],
        );
    }
}

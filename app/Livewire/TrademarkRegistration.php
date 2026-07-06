<?php

namespace App\Livewire;

use App\Models\TrademarkRegistration as TrademarkRegistrationModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class TrademarkRegistration extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public int $maxStep = 3;

    public ?string $schemaError = null;

    public array $trademarkTypes = [
        '2d' => 'Merek 2D (Kata, Logo, Lukisan)',
        '3d' => 'Merek 3D (Bentuk kemasan atau produk)',
        'hologram' => 'Hologram (Gambar)',
        'music' => 'Music (Gambar Notasi)',
    ];

    public array $filingTypes = [
        'madrid' => 'Madrid Protocol',
        'paris' => 'Paris Convention (Conventional Application)',
        'national' => 'National Filing',
    ];

    public array $classes = [];

    public array $classesDriveIds = [];

    public array $regions = [];

    public string $applicant_name = '';

    public ?string $applicant_company = null;

    public string $applicant_email = '';

    public string $active_phone_number = '';

    public ?string $whatsapp_number = null;

    public ?string $wechat_number = null;

    public string $trademark_name = '';

    public string $trademark_type = '2d';

    public string $filing_type = 'madrid';

    public array $selected_classes = [];

    public array $selected_countries = [];

    public ?string $notes = null;

    public ?string $word_mark_text = null;

    public $logo_file;

    public $painting_file;

    public array $shape_files = [];

    public $hologram_image;

    public $music_notation_image;

    public $music_audio_file;

    public $music_video_file;

    public bool $submitted = false;

    public ?int $registrationId = null;

    public array $uploadedFilePaths = [];

    public function mount(): void
    {
        $this->classes = config('form-fields.classes', []);
        $this->classesDriveIds = config('form-fields.classes_drive_id', []);
        $this->regions = config('form-fields.regions', []);
        $this->assertSchema();
    }

    public function updatedTrademarkType(): void
    {
        $this->resetTypeSpecificFields();
        $this->resetValidation();
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function nextStep(): void
    {
        if ($this->schemaError !== null) {
            return;
        }

        $this->validateStep($this->step);

        if ($this->step < $this->maxStep) {
            $this->step++;
        }
    }

    public function submit(): void
    {
        if ($this->schemaError !== null) {
            $this->addError('schema', $this->schemaError);

            return;
        }

        $this->validateStep(1);
        $this->validateStep(2);

        DB::transaction(function (): void {
            $registration = TrademarkRegistrationModel::query()->create([
                'applicant_type' => 'individual',
                'applicant_name' => $this->applicant_name,
                'applicant_company' => $this->applicant_company,
                'applicant_email' => $this->applicant_email,
                'active_phone_number' => $this->active_phone_number,
                'whatsapp_number' => $this->whatsapp_number,
                'wechat_number' => $this->wechat_number,
                'trademark_name' => $this->trademark_name,
                'trademark_type' => $this->trademark_type,
                'filing_type' => $this->filing_type,
                'selected_classes' => $this->selected_classes,
                'selected_countries' => $this->selected_countries,
                'file_paths' => [],
                'notes' => $this->notes,
            ]);

            $folder = sprintf(
                'trademarks/%s/%d',
                $this->getStorageTypeSegment($this->trademark_type),
                $registration->id,
            );

            $filePaths = $this->storeFilesForType($folder);

            $registration->update([
                'file_paths' => $filePaths,
            ]);

            $this->registrationId = $registration->id;
            $this->uploadedFilePaths = $filePaths;
        });

        $this->submitted = true;
        $this->step = $this->maxStep;
    }

    public function validateStep(int $step): void
    {
        $rules = match ($step) {
            1 => [
                'applicant_name' => 'required|string|min:2|max:255',
                'applicant_company' => 'nullable|string|max:255',
                'applicant_email' => 'required|email:rfc,dns|max:255',
                'active_phone_number' => 'required|string|min:8|max:30',
                'whatsapp_number' => 'nullable|string|min:8|max:30',
                'wechat_number' => 'nullable|string|max:100',
                'trademark_name' => 'required|string|min:2|max:255',
                'trademark_type' => 'required|in:2d,3d,hologram,music',
                'filing_type' => 'required|in:madrid,paris,national',
                'selected_classes' => 'required|array|min:1',
                'selected_classes.*' => 'required|string',
                'selected_countries' => 'required|array|min:1',
                'selected_countries.*' => 'required|string|min:2|max:100',
                'notes' => 'nullable|string|max:2000',
            ],
            2 => $this->documentRulesForType(),
            default => [],
        };

        if ($rules !== []) {
            $this->validate($rules);
        }
    }

    protected function documentRulesForType(): array
    {
        return match ($this->trademark_type) {
            '2d' => [
                'word_mark_text' => 'required|string|min:2|max:255',
                'logo_file' => 'required|file|mimes:jpg,jpeg,png,webp|max:4096',
                'painting_file' => 'required|file|mimes:jpg,jpeg,png,webp|max:4096',
            ],
            '3d' => [
                'shape_files' => 'required|array|min:1|max:6',
                'shape_files.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:4096',
            ],
            'hologram' => [
                'hologram_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:4096',
            ],
            'music' => [
                'music_notation_image' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
                'music_audio_file' => 'required|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:15360',
                'music_video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:51200',
            ],
            default => [],
        };
    }

    protected function storeFilesForType(string $folder): array
    {
        return match ($this->trademark_type) {
            '2d' => [
                'word_mark_text' => $this->word_mark_text,
                'logo_file' => $this->storeUploadedFile($this->logo_file, $folder),
                'painting_file' => $this->storeUploadedFile($this->painting_file, $folder),
            ],
            '3d' => [
                'shape_files' => collect($this->shape_files)
                    ->map(fn (TemporaryUploadedFile $file): string => $file->store($folder, 'local'))
                    ->values()
                    ->all(),
            ],
            'hologram' => [
                'hologram_image' => $this->storeUploadedFile($this->hologram_image, $folder),
            ],
            'music' => [
                'music_notation_image' => $this->storeUploadedFile($this->music_notation_image, $folder),
                'music_audio_file' => $this->storeUploadedFile($this->music_audio_file, $folder),
                'music_video_file' => $this->storeUploadedFile($this->music_video_file, $folder),
            ],
            default => [],
        };
    }

    protected function storeUploadedFile(mixed $file, string $folder): ?string
    {
        if (! $file instanceof TemporaryUploadedFile) {
            return null;
        }

        return $file->store($folder, 'local');
    }

    protected function getStorageTypeSegment(string $type): string
    {
        return match ($type) {
            '2d' => '2d',
            '3d' => '3d',
            'hologram' => 'hologram',
            'music' => 'music',
            default => 'others',
        };
    }

    protected function resetTypeSpecificFields(): void
    {
        $this->reset([
            'word_mark_text',
            'logo_file',
            'painting_file',
            'shape_files',
            'hologram_image',
            'music_notation_image',
            'music_audio_file',
            'music_video_file',
        ]);
    }

    protected function assertSchema(): void
    {
        $table = 'trademark_registrations';
        $requiredColumns = [
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
            'selected_countries',
            'file_paths',
        ];

        if (! Schema::hasTable($table)) {
            $this->schemaError = 'Tabel trademark_registrations belum tersedia. Jalankan php artisan migrate terlebih dahulu.';

            return;
        }

        $missingColumns = collect($requiredColumns)
            ->filter(fn (string $column): bool => ! Schema::hasColumn($table, $column))
            ->values()
            ->all();

        if ($missingColumns !== []) {
            $this->schemaError = 'Struktur tabel trademark_registrations belum lengkap. Kolom yang belum ada: '.implode(', ', $missingColumns).'.';
        }
    }

    public function render()
    {
        return view('livewire.trademark-registration');
    }
}

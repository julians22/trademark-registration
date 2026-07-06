<div class="mx-auto px-4 py-10 max-w-5xl">
    <div class="bg-base-100 shadow-xl mt-20 lg:mt-32 border border-base-200 rounded-2xl">
        <div class="p-6 md:p-10">
            <div class="mb-8">
                <h1 class="font-bold text-2xl md:text-3xl">Form Pendaftaran Merek</h1>
                <p class="mt-2 text-base-content/70">Lengkapi data sesuai jenis merek yang Anda daftarkan.</p>
            </div>

            @if ($schemaError)
                <div class="mb-6 alert alert-error">
                    <span>{{ $schemaError }}</span>
                </div>
            @endif

            @if ($submitted)
                <div class="mb-6 alert alert-success">
                    <span>
                        Pendaftaran berhasil disimpan dengan ID #{{ $registrationId }}.
                    </span>
                </div>
            @endif

            <ul class="mb-8 w-full steps steps-vertical md:steps-horizontal">
                <li class="step {{ $step >= 1 ? 'step-primary' : '' }}">Informasi Dasar</li>
                <li class="step {{ $step >= 2 ? 'step-primary' : '' }}">Dokumen</li>
                <li class="step {{ $step >= 3 ? 'step-primary' : '' }}">Ringkasan</li>
            </ul>

            @if ($step === 1)
                <div class="space-y-5">
                    <div class="gap-4 grid md:grid-cols-2">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Name*</legend>
                            <input type="text" wire:model.live="applicant_name" class="w-full input input-bordered" placeholder="Nama pemohon" />
                            @error('applicant_name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Company</legend>
                            <input type="text" wire:model.live="applicant_company" class="w-full input input-bordered" placeholder="Nama perusahaan (opsional)" />
                            @error('applicant_company') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Email*</legend>
                            <input type="email" wire:model.live="applicant_email" class="w-full input input-bordered" placeholder="email@domain.com" />
                            @error('applicant_email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Active Phone Number*</legend>
                            <input type="text" wire:model.live="active_phone_number" class="w-full input input-bordered" placeholder="Contoh: 08123456789" />
                            @error('active_phone_number') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Whatsapp Number</legend>
                            <input type="text" wire:model.live="whatsapp_number" class="w-full input input-bordered" placeholder="Nomor WhatsApp" />
                            @error('whatsapp_number') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">WeChat Number</legend>
                            <input type="text" wire:model.live="wechat_number" class="w-full input input-bordered" placeholder="ID WeChat" />
                            @error('wechat_number') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Nama Merek</legend>
                        <input type="text" wire:model.live="trademark_name" class="w-full input input-bordered" placeholder="Contoh: Nusa Raya" />
                        @error('trademark_name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Jenis Merek</legend>
                        <select wire:model.live="trademark_type" class="w-full select-bordered select">
                            @foreach ($trademarkTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trademark_type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Tipe Pendaftaran</legend>
                        <select wire:model.live="filing_type" class="w-full select-bordered select">
                            @foreach ($filingTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('filing_type') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Kelas Merek (Nice Classification)</legend>
                        <div
                            x-data="{ detailUrl: '', detailTitle: '' }"
                            class="border border-base-200 rounded-xl max-h-80 overflow-auto">
                            <table class="table table-pin-rows table-zebra">
                                <thead>
                                    <tr>
                                        <th class="w-24">Pilih</th>
                                        <th class="w-28">Class</th>
                                        <th>Deskripsi</th>
                                        <th class="w-32">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @foreach ($classes as $key => $description)
                                    <tr>
                                        <td>
                                            <input wire:model.live="selected_classes" type="checkbox" value="{{ $key }}" class="checkbox checkbox-sm" />
                                        </td>
                                        <td class="font-semibold">{{ $key }}</td>
                                        <td class="text-sm leading-6">{{ $description }}</td>
                                        <td>
                                            @php $classDriveId = $classesDriveIds[$key] ?? null; @endphp
                                            @if (!empty($classDriveId))
                                                <button
                                                    type="button"
                                                    class="link link-warning"
                                                    x-on:click="detailUrl = 'https://drive.google.com/file/d/{{ $classDriveId }}/preview'; detailTitle = '{{ $key }}'; $refs.classDetailModal.showModal()"
                                                >
                                                    view detail
                                                </button>
                                            @else
                                                <span class="text-xs text-base-content/40">-</span>
                                            @endif
                                        </td>
                                    </tr>
                            @endforeach
                                </tbody>
                            </table>

                            <dialog x-ref="classDetailModal" class="modal">
                                <div class="w-11/12 max-w-5xl modal-box">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="font-bold text-lg" x-text="'Detail ' + detailTitle"></h3>
                                        <form method="dialog">
                                            <button class="btn btn-sm btn-circle btn-ghost">x</button>
                                        </form>
                                    </div>

                                    <div class="border border-base-200 rounded-lg aspect-video overflow-hidden">
                                        <iframe
                                            x-bind:src="detailUrl"
                                            class="w-full h-full"
                                            allow="autoplay"
                                            loading="lazy"
                                        ></iframe>
                                    </div>
                                </div>
                                <form method="dialog" class="modal-backdrop">
                                    <button>close</button>
                                </form>
                            </dialog>
                        </div>
                        @error('selected_classes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        @error('selected_classes.*') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Negara Tujuan Pendaftaran</legend>
                        <div class="space-y-3">
                            @foreach ($regions as $region)
                                <div class="p-3 border border-base-200 rounded-xl">
                                    <p class="mb-2 font-semibold text-sm">{{ $region['name'] }}</p>
                                    <div class="gap-2 grid sm:grid-cols-2">
                                        @foreach ($region['countries'] as $country)
                                            <label class="justify-start gap-3 cursor-pointer label">
                                                <input wire:model.live="selected_countries" type="checkbox" value="{{ $country }}" class="checkbox checkbox-sm" />
                                                <span class="text-sm">{{ $country }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('selected_countries') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        @error('selected_countries.*') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Catatan Tambahan (Opsional)</legend>
                        <textarea wire:model.live="notes" class="w-full textarea textarea-bordered" rows="3" placeholder="Keterangan singkat jika diperlukan"></textarea>
                        @error('notes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </fieldset>
                </div>
            @endif

            @if ($step === 2)
                <div class="space-y-5">
                    @if ($trademark_type === '2d')
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Merek 2D - Kata</legend>
                            <input type="text" wire:model.live="word_mark_text" class="w-full input input-bordered" placeholder="Contoh: NUSA RAYA" />
                            @error('word_mark_text') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Merek 2D - Logo</legend>
                            <input type="file" wire:model="logo_file" class="w-full file-input file-input-bordered" accept=".jpg,.jpeg,.png,.webp" />
                            <span class="text-info text-sm" wire:loading wire:target="logo_file">Mengunggah logo...</span>
                            @error('logo_file') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Merek 2D - Lukisan</legend>
                            <input type="file" wire:model="painting_file" class="w-full file-input file-input-bordered" accept=".jpg,.jpeg,.png,.webp" />
                            <span class="text-info text-sm" wire:loading wire:target="painting_file">Mengunggah lukisan...</span>
                            @error('painting_file') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>
                    @endif

                    @if ($trademark_type === '3d')
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Merek 3D - Bentuk Kemasan/Produk (1-6 gambar)</legend>
                            <input type="file" wire:model="shape_files" class="w-full file-input file-input-bordered" accept=".jpg,.jpeg,.png,.webp" multiple />
                            <span class="text-info text-sm" wire:loading wire:target="shape_files">Mengunggah gambar bentuk 3D...</span>
                            @error('shape_files') <span class="text-error text-sm">{{ $message }}</span> @enderror
                            @error('shape_files.*') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>
                    @endif

                    @if ($trademark_type === 'hologram')
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Hologram - Gambar</legend>
                            <input type="file" wire:model="hologram_image" class="w-full file-input file-input-bordered" accept=".jpg,.jpeg,.png,.webp" />
                            <span class="text-info text-sm" wire:loading wire:target="hologram_image">Mengunggah gambar hologram...</span>
                            @error('hologram_image') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>
                    @endif

                    @if ($trademark_type === 'music')
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Music - Gambar Notasi</legend>
                            <input type="file" wire:model="music_notation_image" class="w-full file-input file-input-bordered" accept=".jpg,.jpeg,.png,.webp,.pdf" />
                            <span class="text-info text-sm" wire:loading wire:target="music_notation_image">Mengunggah notasi musik...</span>
                            @error('music_notation_image') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Music - Audio</legend>
                            <input type="file" wire:model="music_audio_file" class="w-full file-input file-input-bordered" accept=".mp3,.wav,.ogg,.m4a,.aac,.flac" />
                            <span class="text-info text-sm" wire:loading wire:target="music_audio_file">Mengunggah audio musik...</span>
                            @error('music_audio_file') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Music - Video (Opsional)</legend>
                            <input type="file" wire:model="music_video_file" class="w-full file-input file-input-bordered" accept=".mp4,.mov,.avi,.mkv,.webm" />
                            <span class="text-info text-sm" wire:loading wire:target="music_video_file">Mengunggah video musik...</span>
                            @error('music_video_file') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </fieldset>
                    @endif
                </div>
            @endif

            @if ($step === 3)
                <div class="space-y-5">
                    <div class="border border-base-200 rounded-xl overflow-x-auto">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Nama Pemohon</th>
                                    <td>{{ $applicant_name }}</td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td>{{ $applicant_company ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $applicant_email }}</td>
                                </tr>
                                <tr>
                                    <th>Active Phone Number</th>
                                    <td>{{ $active_phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>Whatsapp Number</th>
                                    <td>{{ $whatsapp_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>WeChat Number</th>
                                    <td>{{ $wechat_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Merek</th>
                                    <td>{{ $trademark_name }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Merek</th>
                                    <td>{{ $trademarkTypes[$trademark_type] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Pendaftaran</th>
                                    <td>{{ $filingTypes[$filing_type] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ $selected_classes !== [] ? implode(', ', $selected_classes) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Negara</th>
                                    <td>{{ $selected_countries !== [] ? implode(', ', $selected_countries) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $notes ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if (! empty($uploadedFilePaths))
                        <div class="alert alert-info">
                            <div>
                                <p class="font-semibold">Lokasi file tersimpan:</p>
                                <pre class="mt-2 text-xs whitespace-pre-wrap">{{ json_encode($uploadedFilePaths, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-base-content/70">Periksa kembali data Anda lalu klik Submit.</p>
                    @endif
                </div>
            @endif

            <div class="flex sm:flex-row flex-col-reverse justify-between gap-3 mt-8">
                <button type="button" class="btn" wire:click="previousStep" @disabled($step === 1)>Sebelumnya</button>

                <div class="flex justify-end">
                    @if ($step < $maxStep)
                        <button type="button" class="btn btn-primary" wire:click="nextStep" @disabled($schemaError !== null)>Lanjut</button>
                    @else
                        <button type="button" class="btn btn-primary" wire:click="submit" wire:loading.attr="disabled" @disabled($schemaError !== null || $submitted)>
                            <span wire:loading.remove wire:target="submit">Submit Pendaftaran</span>
                            <span wire:loading wire:target="submit">Menyimpan...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

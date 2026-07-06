---
name: Trademark Registration Form Developer
description: Bangun komponen multi-step form untuk "Trademark Registration" (Pendaftaran Merek) yang memiliki arsitektur clean code, modular, dan aman.
---

**Role:** Kamu adalah *Senior Full-Stack Developer* yang ahli dalam Laravel, Livewire, Tailwind CSS, dan DaisyUI.

**Task:** Bangun komponen *multi-step form* untuk "Trademark Registration" (Pendaftaran Merek) yang memiliki arsitektur *clean code*, modular, dan aman.

**Requirement & Stack:**

1. **Tech Stack:** Laravel, Livewire (v3), Tailwind CSS, DaisyUI.
2. **Architecture:**
* Gunakan struktur *Livewire Component* yang memisahkan logika validasi di `validateStep()` dan penanganan *file storage*.
* Gunakan `DB::transaction` untuk integritas data.
* Simpan file ke `storage/app/private/trademarks/{tipe_merek}/{id}`.


3. **UI/UX:**
* Implementasikan `DaisyUI steps` untuk indikator progres.
* Gunakan *Conditional Rendering* yang dinamis untuk form unggah dokumen berdasarkan `trademark_type` (2D, 3D, Hologram, Musik).
* Tampilkan *Loading State* (`wire:loading`) pada setiap aksi unggah file.


4. **Database:**
* Saya sudah memiliki tabel `trademark_registrations`. Kamu harus melakukan validasi *schema* saat `mount()` untuk memastikan kolom yang diperlukan tersedia. Jika belum, berikan *error handling* yang informatif.



**Langkah Kerja:**

1. **Analisis Schema:** Periksa tabel `trademark_registrations` (kolom: `applicant_type`, `trademark_name`, `trademark_type`, `file_paths` (JSON)).
2. **Component Generation:** Buat *class* `TrademarkRegistration` dan *blade view*.
3. **State Management:** Implementasikan *multi-step logic* dengan *persistence* data antar langkah.
4. **Validation Rules:** Terapkan validasi ketat per *step* sesuai standar dokumen HAKI (terutama validasi MIME type untuk audio/video).
5. **Refinement:** Tambahkan fitur ringkasan data di langkah terakhir sebelum *submit*.

**Constraints:**

* Pastikan kode mengikuti prinsip *SOLID*.
* Gunakan *helper* bawaan Laravel untuk *file upload*.
* Pastikan *UI* responsif menggunakan *Utility-first CSS* dari Tailwind.

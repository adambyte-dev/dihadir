<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:255'],
            'unit_kerja' => ['required', 'string', 'max:255'],
            'kabupaten_kota' => ['required', 'string', 'max:255'],
            'signature' => ['required', 'string', 'starts_with:data:image/png;base64,', 'max:500000'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*' => ['nullable'],
            'custom_fields.*.*' => ['nullable', 'string', 'max:1000'],
            'custom_files' => ['nullable', 'array'],
            'custom_files.*' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx'], // Max 5MB per file
        ];
    }

    /**
     * Custom messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'unit_kerja.required' => 'Unit kerja / instansi wajib diisi.',
            'kabupaten_kota.required' => 'Kabupaten / Kota wajib dipilih.',
            'signature.required' => 'Tanda tangan digital wajib dibubuhkan sebagai bukti kehadiran fisik.',
            'signature.starts_with' => 'Format tanda tangan digital tidak valid. Silakan ulangi tanda tangan.',
            'signature.max' => 'Ukuran tanda tangan digital melebihi batas wajar.',
            'custom_files.*.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'custom_files.*.mimes' => 'Format lampiran harus berupa gambar (JPG/PNG/WebP), PDF, DOC, atau DOCX.',
        ];
    }
}

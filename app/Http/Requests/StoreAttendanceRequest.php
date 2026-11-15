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
     * Validasi dinamis dijalankan di controller berdasarkan field aktif formulir.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nip' => ['nullable', 'string', 'max:64'],
            'name' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'string', 'max:500000'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*' => ['nullable'],
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
            'custom_files.*.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'custom_files.*.mimes' => 'Format lampiran harus berupa gambar (JPG/PNG/WebP), PDF, DOC, atau DOCX.',
        ];
    }
}

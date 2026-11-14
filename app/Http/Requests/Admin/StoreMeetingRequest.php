<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin() && $this->user()->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'organizer' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'status' => ['required', 'in:draft,scheduled,ongoing,completed,archived'],
            'form_status' => ['required', 'in:draft,published,closed'],
            'allow_anonymous' => ['nullable', 'boolean'],
            'admin_ids' => ['nullable', 'array'],
            'admin_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->whereIn('role', ['admin', 'super_admin'])->where('is_active', true)
                ),
            ],
        ];
    }

    /**
     * Custom messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Nama kegiatan / pertemuan wajib diisi.',
            'organizer.required' => 'Penyelenggara pertemuan wajib diisi.',
            'location.required' => 'Lokasi / ruangan pertemuan wajib diisi.',
            'meeting_date.required' => 'Tanggal pertemuan wajib ditentukan.',
            'start_time.required' => 'Waktu mulai pertemuan wajib ditentukan.',
        ];
    }

    /**
     * Prepare inputs for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'allow_anonymous' => $this->boolean('allow_anonymous'),
            'organizer' => $this->input('organizer') ?: 'SMKN 1 Majalengka',
        ]);
    }
}

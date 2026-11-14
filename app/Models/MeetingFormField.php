<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'field_key',
        'label',
        'type',
        'description',
        'placeholder',
        'is_required',
        'options',
        'order_index',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'array',
            'order_index' => 'integer',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttendanceCustomAnswer::class, 'meeting_form_field_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'text' => 'Teks Singkat (Short Text)',
            'textarea' => 'Paragraf / Catatan (Long Text)',
            'number' => 'Angka / Nominal (Number)',
            'email' => 'Alamat Email',
            'phone' => 'Nomor WhatsApp / Telepon',
            'select' => 'Dropdown Pilihan (Select)',
            'radio' => 'Pilihan Tunggal (Radio)',
            'checkbox' => 'Kotak Centang (Checkbox)',
            'file' => 'Unggah Foto / Dokumen (File Upload)',
            'signature' => 'Tanda Tangan Digital (Signature Canvas)',
            'date' => 'Tanggal (Date)',
            'time' => 'Waktu / Jam (Time)',
            'scale' => 'Skala Linier (Rating 1-5 / 1-10)',
            default => ucfirst($this->type),
        };
    }
}

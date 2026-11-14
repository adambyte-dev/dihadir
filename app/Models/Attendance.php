<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'participant_id',
        'nip',
        'name',
        'unit_kerja',
        'kabupaten_kota',
        'signature_path',
        'attended_date',
        'attended_time',
        'ip_address',
        'user_agent',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attended_date' => 'date',
        ];
    }

    /**
     * Meeting relationship.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Participant relationship.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Custom field answers relationship.
     */
    public function customAnswers(): HasMany
    {
        return $this->hasMany(AttendanceCustomAnswer::class, 'attendance_id');
    }

    /**
     * Scope for valid attendance submissions.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', 'valid');
    }
}

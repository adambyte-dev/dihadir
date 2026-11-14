<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCustomAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'meeting_form_field_id',
        'field_key',
        'field_label',
        'answer_value',
        'file_path',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(MeetingFormField::class, 'meeting_form_field_id');
    }
}

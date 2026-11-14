<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'public_token',
        'description',
        'organizer',
        'location',
        'meeting_date',
        'start_time',
        'end_time',
        'max_participants',
        'status',
        'form_status',
        'allow_anonymous',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'allow_anonymous' => 'boolean',
            'max_participants' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meeting) {
            if (empty($meeting->slug)) {
                $meeting->slug = Str::slug($meeting->title) . '-' . Str::lower(Str::random(6));
            }
            if (empty($meeting->public_token)) {
                $meeting->public_token = Str::random(32);
            }
        });
    }

    /**
     * Creator relationship.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Assigned Admins relationship.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_admins')->withTimestamps();
    }

    /**
     * Invited/registered participants relationship.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'meeting_participants')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Attendances / submissions relationship (Fase 4 & 5).
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'meeting_id');
    }

    /**
     * Scope for filtering meetings authorized for the given user.
     * Super Admin can access all meetings. Admin can only access assigned/created meetings.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('admins', function ($sub) use ($user) {
                  $sub->where('user_id', $user->id);
              });
        });
    }

    /**
     * Scope for active meetings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['scheduled', 'ongoing']);
    }

    /**
     * Check if user is authorized to manage this meeting.
     */
    public function isManagedBy(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $this->created_by === $user->id || $this->admins->contains($user->id);
    }

    /**
     * Status Label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draf',
            'scheduled' => 'Terjadwal',
            'ongoing' => 'Berlangsung',
            'completed' => 'Selesai',
            'archived' => 'Diarsipkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Status Badge Variant for <x-badge>.
     */
    public function getStatusBadgeVariantAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'default',
            'scheduled' => 'info',
            'ongoing' => 'primary',
            'completed' => 'success',
            'archived' => 'secondary',
            default => 'default',
        };
    }

    /**
     * Form Status Label in Indonesian.
     */
    public function getFormStatusLabelAttribute(): string
    {
        return match ($this->form_status) {
            'draft' => 'Draf Formulir',
            'published' => 'Terbit (Buka)',
            'closed' => 'Ditutup',
            default => ucfirst($this->form_status),
        };
    }

    /**
     * Form Status Badge Variant for <x-badge>.
     */
    public function getFormStatusBadgeVariantAttribute(): string
    {
        return match ($this->form_status) {
            'draft' => 'default',
            'published' => 'success',
            'closed' => 'danger',
            default => 'default',
        };
    }

    /**
     * Custom form fields relationship.
     */
    public function formFields(): HasMany
    {
        return $this->hasMany(MeetingFormField::class, 'meeting_id')->orderBy('order_index');
    }

    /**
     * Active custom form fields for public attendance.
     */
    public function activeFormFields(): HasMany
    {
        return $this->hasMany(MeetingFormField::class, 'meeting_id')
            ->where('is_active', true)
            ->orderBy('order_index');
    }

    /**
     * Public attendance URL.
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/p/' . $this->public_token);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nip',
        'password',
        'role',
        'is_active',
        'avatar_style',
        'avatar_seed',
    ];

    /**
     * Get avatar SVG representation using DiceBear
     */
    public function getAvatarSvgAttribute(): string
    {
        $style = $this->avatar_style ?: 'lorelei';
        $seed = $this->avatar_seed ?: ($this->name ?: 'User-' . $this->id);

        return \App\Services\AvatarService::renderSvg($style, $seed);
    }

    /**
     * Get avatar direct URL via registered route /avatar/user/{id}
     */
    public function getAvatarUrlAttribute(): string
    {
        return route('avatar.user', ['user' => $this->id]);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Meetings created by this admin.
     */
    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    /**
     * Meetings assigned to this admin.
     */
    public function assignedMeetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_admins')->withTimestamps();
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user has Admin privileges (Admin or Super Admin)
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }
}

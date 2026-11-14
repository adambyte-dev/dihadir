<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    /**
     * Perform pre-authorization checks.
     * Super Admin can manage all meetings across the system.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin() && $user->is_active) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any meetings.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->is_active;
    }

    /**
     * Determine whether the user can view the meeting.
     */
    public function view(User $user, Meeting $meeting): bool
    {
        return $meeting->isManagedBy($user) && $user->is_active;
    }

    /**
     * Determine whether the user can create meetings.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->is_active;
    }

    /**
     * Determine whether the user can update the meeting.
     */
    public function update(User $user, Meeting $meeting): bool
    {
        return $meeting->isManagedBy($user) && $user->is_active;
    }

    /**
     * Determine whether the user can delete the meeting.
     */
    public function delete(User $user, Meeting $meeting): bool
    {
        return $meeting->isManagedBy($user) && $user->is_active;
    }
}

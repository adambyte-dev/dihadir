<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit log entry for the currently authenticated user.
     */
    public static function log(string $action, ?Model $target = null, array $details = []): AuditLog
    {
        return self::logFor(Auth::id(), $action, $target, $details);
    }

    /**
     * Record an audit log entry with an explicit actor.
     * Use a null actor for events that occur before authentication succeeds.
     */
    public static function logFor(?int $userId, string $action, ?Model $target = null, array $details = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target ? $target->getKey() : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => empty($details) ? null : $details,
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an activity.
     *
     * @param string $event
     * @param mixed $auditable
     * @param array|null $old
     * @param array|null $new
     * @return ActivityLog
     */
    public function log(string $event, $auditable = null, ?array $old = null, ?array $new = [])
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'event_type' => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->id : null,
            'old_values' => $this->sanitizeValues($old),
            'new_values' => $this->sanitizeValues($new),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Sanitize values to remove sensitive data.
     */
    private function sanitizeValues(?array $values): ?array
    {
        if (!$values) return null;

        $sensitiveFields = ['password', 'token', 'secret', 'key', 'password_confirmation'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[REDACTED]';
            }
        }

        return $values;
    }
}

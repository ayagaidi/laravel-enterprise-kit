<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditService
{
    public function record(
        string $action,
        ?Model $subject = null,
        array $oldValues = [],
        array $newValues = [],
        ?Request $request = null,
    ): AuditLog {
        $request ??= request();

        return AuditLog::query()->create([
            'actor_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'old_values' => $this->redact($oldValues),
            'new_values' => $this->redact($newValues),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 500, ''),
            'request_id' => $request?->headers->get('X-Request-ID') ?: (string) Str::uuid(),
            'created_at' => now(),
        ]);
    }

    private function redact(array $values): array
    {
        foreach (['password', 'password_confirmation', 'token', 'secret'] as $key) {
            if (array_key_exists($key, $values)) {
                $values[$key] = '[REDACTED]';
            }
        }

        return $values;
    }
}

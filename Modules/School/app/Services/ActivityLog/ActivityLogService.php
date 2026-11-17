<?php

namespace Modules\School\Services\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\School\Models\ActivityLog;
use Modules\School\Support\SchoolTenancy;
use Throwable;

/**
 * The one place that writes to the school `activity_logs` table (business/audit trail,
 * separate from the technical `Log` facade trail).
 */
class ActivityLogService
{
    /**
     * Record a business/audit activity. Never throws — a logging failure must
     * not break the write operation that triggered it.
     */
    public function log(?Model $actor, string $action, ?Model $subject = null, array $properties = [], ?int $companyId = null): void
    {
        try {
            ActivityLog::create([
                'company_id' => $companyId ?? SchoolTenancy::id() ?? $actor?->getAttribute('company_id'),
                'actor_type' => $actor?->getMorphClass(),
                'actor_id' => $actor?->getKey(),
                'action' => $action,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'description' => $properties['description'] ?? null,
                'properties' => $properties,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (Throwable $e) {
            Log::error('School ActivityLogService::log failed', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

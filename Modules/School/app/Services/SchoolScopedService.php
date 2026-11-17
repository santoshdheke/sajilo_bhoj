<?php

namespace Modules\School\Services;

use Modules\School\Services\ActivityLog\ActivityLogService;
use Modules\School\Support\SchoolTenancy;
use RuntimeException;

/**
 * Shared plumbing for Admin services that own school-scoped rows: the school always
 * comes from the host (SchoolTenancy), never from a request payload.
 */
abstract class SchoolScopedService
{
    public function __construct(
        protected readonly ActivityLogService $activityLog,
    ) {}

    protected function schoolId(): int
    {
        $school = SchoolTenancy::company();

        if (! $school) {
            throw new RuntimeException('No active school for this request.');
        }

        return $school->id;
    }
}

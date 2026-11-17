<?php

namespace Modules\School\Support;

use Modules\School\Models\Company;

/**
 * The school (company) of the current request, bound by Modules\School\Http\Middleware\ResolveSchool.
 * Replaces sms's App\Services\Tenant\TenantContext: the school always comes from the host,
 * never from a request payload.
 */
class SchoolTenancy
{
    public const BINDING = 'school.tenant';

    public static function company(): ?Company
    {
        return app()->bound(self::BINDING) ? app(self::BINDING) : null;
    }

    public static function id(): ?int
    {
        return self::company()?->id;
    }
}

<?php

namespace Modules\Restaurant\Support;

use Modules\Restaurant\Models\Branch;
use Modules\Restaurant\Models\Company;

/**
 * Reads the active tenant company/branch bound into the container by
 * Modules\Restaurant\Http\Middleware\ResolveTenant for the current request.
 */
class Tenancy
{
    public static function company(): ?Company
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }

    public static function branch(): ?Branch
    {
        return app()->bound('tenant_branch') ? app('tenant_branch') : null;
    }
}

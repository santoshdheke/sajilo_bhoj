<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Company;

/**
 * Reads the active tenant company/branch bound into the container by
 * App\Http\Middleware\ResolveTenant for the current request.
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

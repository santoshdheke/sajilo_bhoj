<?php

namespace Modules\Shop\Support;

use Modules\Shop\Models\Company;

/**
 * The shop company of the current request, bound by Modules\Shop\Http\Middleware\ResolveShop.
 * (Replaces multiple_pasal's company() helper / session company.)
 */
class ShopTenancy
{
    public const BINDING = 'shop.tenant';

    public static function company(): ?Company
    {
        return app()->bound(self::BINDING) ? app(self::BINDING) : null;
    }

    public static function id(): ?int
    {
        return self::company()?->id;
    }
}

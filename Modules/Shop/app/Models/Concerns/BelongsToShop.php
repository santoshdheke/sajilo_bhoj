<?php

namespace Modules\Shop\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Shop\Models\Company;
use Modules\Shop\Support\ShopTenancy;

/**
 * Rows owned by one shop: queries only see the current shop's rows and new rows get its
 * company_id (multiple_pasal's CompanyTrait + CompanyScope).
 */
trait BelongsToShop
{
    protected static function bootBelongsToShop(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if ($companyId = ShopTenancy::id()) {
                $builder->where($builder->qualifyColumn('company_id'), $companyId);
            }
        });

        static::creating(function (Model $model) {
            if (! $model->company_id && ($companyId = ShopTenancy::id())) {
                $model->company_id = $companyId;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if ($company = Tenancy::company()) {
                $builder->where($builder->qualifyColumn('company_id'), $company->id);
            }
        });

        static::creating(function (Model $model) {
            if (! $model->company_id && ($company = Tenancy::company())) {
                $model->company_id = $company->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

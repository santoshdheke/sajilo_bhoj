<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if ($branch = Tenancy::branch()) {
                $builder->where($builder->qualifyColumn('branch_id'), $branch->id);
            }
        });

        static::creating(function (Model $model) {
            if (! $model->branch_id && ($branch = Tenancy::branch())) {
                $model->branch_id = $branch->id;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}

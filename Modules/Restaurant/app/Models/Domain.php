<?php

namespace Modules\Restaurant\Models;

use App\Models\Domain as BaseDomain;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Common-database domain whose company is loaded as a restaurant Company. */
class Domain extends BaseDomain
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

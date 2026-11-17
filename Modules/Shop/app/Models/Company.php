<?php

namespace Modules\Shop\Models;

use App\Models\Company as BaseCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shop view of a common-database company: only company_type "shop",
 * with the shop relations on top of App\Models\Company.
 */
class Company extends BaseCompany
{
    protected static function booted(): void
    {
        static::addGlobalScope('shop', fn (Builder $q) => $q->where($q->qualifyColumn('company_type'), self::TYPE_SHOP));
        static::creating(fn (self $company) => $company->company_type ??= self::TYPE_SHOP);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

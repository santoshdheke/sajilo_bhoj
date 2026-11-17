<?php

namespace Modules\School\Models;

use App\Models\Company as BaseCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Enums\UserRoleEnum;

/**
 * School view of a common-database company: only company_type "school", with the school
 * relations on top of App\Models\Company. This is sms's App\Models\School (the tenant).
 */
class Company extends BaseCompany
{
    protected static function booted(): void
    {
        static::addGlobalScope('school', fn (Builder $q) => $q->where($q->qualifyColumn('company_type'), self::TYPE_SCHOOL));
        static::creating(fn (self $company) => $company->company_type ??= self::TYPE_SCHOOL);
    }

    /**
     * The admins, teachers and students who log in on this school's domain.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Whether a school admin has registered yet.
     */
    public function hasAdmin(): bool
    {
        return $this->users()->where('role', UserRoleEnum::Admin->value)->exists();
    }
}

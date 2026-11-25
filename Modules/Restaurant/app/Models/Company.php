<?php

namespace Modules\Restaurant\Models;

use App\Models\Company as BaseCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Restaurant view of a common-database company: only company_type "restaurant",
 * with the restaurant relations (branches, users, menu...) on top of App\Models\Company.
 */
class Company extends BaseCompany
{
    protected static function booted(): void
    {
        static::addGlobalScope('restaurant', fn (Builder $q) => $q->where($q->qualifyColumn('company_type'), self::TYPE_RESTAURANT));
        static::creating(fn (self $company) => $company->company_type ??= self::TYPE_RESTAURANT);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Client companies this company added (Refer & Earn). */
    public function clients(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by_company_id');
    }

    /** The company that added this one, if any. */
    public function referrer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by_company_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** Plan of the latest subscription, looked up once per request. */
    public function currentPlan(): ?Plan
    {
        return $this->currentSubscription()?->plan;
    }

    /** Latest subscription (with its plan), looked up once per request. */
    public function currentSubscription(): ?Subscription
    {
        if (! array_key_exists('current_subscription', $this->relations)) {
            $this->setRelation('current_subscription', Subscription::withoutGlobalScope('company')->with('plan')
                ->where('company_id', $this->id)->latest('id')->first());
        }

        return $this->relations['current_subscription'];
    }

    /**
     * Remove a sign-up that was never verified, with everything it created
     * (users first: they are only unlinked, not deleted, when a company goes; then copied menu photos).
     */
    public function purgeUnverified(): void
    {
        $branchIds = Branch::withoutGlobalScopes()->where('company_id', $this->id)->pluck('id');
        MenuCategory::withoutGlobalScopes()->whereIn('branch_id', $branchIds)->whereNotNull('image')->pluck('image')
            ->each(fn ($path) => \Illuminate\Support\Facades\Storage::disk('public')->delete($path));

        User::where('company_id', $this->id)->delete();
        $this->delete();
    }
}

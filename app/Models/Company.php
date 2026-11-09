<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'email', 'phone', 'address', 'logo', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** Plan of the latest subscription, looked up once per request. */
    public function currentPlan(): ?Plan
    {
        if (! array_key_exists('current_plan', $this->relations)) {
            $sub = Subscription::withoutGlobalScope('company')->with('plan')
                ->where('company_id', $this->id)->latest('id')->first();
            $this->setRelation('current_plan', $sub?->plan);
        }

        return $this->relations['current_plan'];
    }

    /** A workspace is live once its owner has verified their email. Looked up once per request. */
    public function isVerified(): bool
    {
        return $this->verified ??= User::where('company_id', $this->id)->whereNotNull('email_verified_at')->exists();
    }

    /** Per-request cache for isVerified() (a plain property, never saved). */
    private ?bool $verified = null;

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

<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Role;
use App\Models\User;

/**
 * The plan's limits on menu items, staff accounts and customers.
 * A limit of null means unlimited; a company without a plan is not limited.
 * Limits are checked when something new is added — existing records are never removed.
 */
class PlanLimits
{
    /** key => [plan column, label] */
    public const RESOURCES = [
        'items'     => ['item_limit', 'menu items'],
        'staff'     => ['staff_limit', 'staff accounts'],
        'customers' => ['customer_limit', 'customers'],
    ];

    public static function limit(?Company $company, string $key): ?int
    {
        $limit = $company?->currentPlan()?->{self::RESOURCES[$key][0]};

        return $limit === null ? null : (int) $limit;
    }

    public static function used(?Company $company, string $key): int
    {
        if (! $company) {
            return 0;
        }

        return match ($key) {
            'items' => MenuItem::whereHas('category', fn ($q) => $q->withoutGlobalScopes()
                ->whereIn('branch_id', Branch::withoutGlobalScopes()->where('company_id', $company->id)->select('id')))->count(),
            // Owners run the restaurant; only the staff they add count toward the limit.
            'staff' => User::where('company_id', $company->id)
                ->whereNotIn('role_id', Role::withoutGlobalScopes()->where('company_id', $company->id)->get()->filter->isOwner()->pluck('id'))
                ->count(),
            'customers' => Customer::withoutGlobalScopes()->where('company_id', $company->id)->count(),
        };
    }

    public static function canAdd(?Company $company, string $key): bool
    {
        $limit = self::limit($company, $key);

        return $limit === null || self::used($company, $key) < $limit;
    }

    /** Message for the person who hit the limit (admin side). */
    public static function message(?Company $company, string $key): string
    {
        $plan  = $company?->currentPlan()?->name ?? 'current';
        $label = self::RESOURCES[$key][1];

        return "Your {$plan} plan allows " . self::limit($company, $key) . " {$label}. Upgrade your plan to add more.";
    }
}

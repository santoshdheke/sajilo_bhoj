<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Role;
use App\Models\Setting;

/**
 * Staff roles every restaurant starts with (besides Owner). The owner can change or delete them later.
 */
class DefaultRoles
{
    public const ROLES = [
        'Manager' => ['Runs the restaurant day to day: everything except staff accounts and website settings.', [
            'dashboard.view', 'orders.manage', 'kitchen.manage', 'tables.manage', 'billing.view', 'payments.manage',
            'customers.manage', 'menu.manage', 'inventory.manage', 'suppliers.manage', 'purchase_orders.manage', 'reports.view',
        ]],
        'Waiter' => ['Takes orders at the tables and sends them to the kitchen.', [
            'dashboard.view', 'orders.manage', 'kitchen.manage', 'tables.manage',
        ]],
        'Receptionist' => ['Welcomes guests, handles bookings, bills and payments.', [
            'dashboard.view', 'tables.manage', 'orders.manage', 'billing.view', 'payments.manage', 'customers.manage',
        ]],
        'Chef' => ['Cooks the orders, keeps the menu and stock up to date.', [
            'dashboard.view', 'kitchen.manage', 'menu.manage', 'inventory.manage',
        ]],
    ];

    public static function seed(Company $company): void
    {
        foreach (self::ROLES as $name => [$description, $permissions]) {
            Role::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                ['description' => $description, 'permissions' => $permissions],
            );
        }
        Setting::putValue($company->id, 'default_roles_added', '1');
    }

    /** Companies created before default roles existed get them once (deleted ones don't come back). */
    public static function ensure(Company $company): void
    {
        if (! Setting::getValue($company->id, 'default_roles_added')) {
            self::seed($company);
        }
    }
}

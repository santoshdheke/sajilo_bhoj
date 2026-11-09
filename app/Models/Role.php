<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'description', 'permissions'];

    protected $casts = ['permissions' => 'array'];

    /** Every permission a role can be given (key => label shown on the Roles screen). */
    public const PERMISSIONS = [
        'dashboard.view'         => 'Dashboard',
        'orders.manage'          => 'Orders',
        'kitchen.manage'         => 'Kitchen Display',
        'tables.manage'          => 'Tables & Bookings',
        'billing.view'           => 'Billing',
        'payments.manage'        => 'Payments',
        'customers.manage'       => 'Customers',
        'menu.manage'            => 'Menu',
        'inventory.manage'       => 'Inventory',
        'suppliers.manage'       => 'Suppliers',
        'purchase_orders.manage' => 'Purchase Orders',
        'reports.view'           => 'Reports',
        'users.manage'           => 'Users & Roles',
        'settings.manage'        => 'Website Settings',
    ];

    /** Everyone lands on the dashboard after signing in, so every role keeps it. */
    public const ALWAYS = ['dashboard.view'];

    /** The Owner role has every permission ('*'); it can't be edited or deleted. */
    public function isOwner(): bool
    {
        return in_array('*', $this->permissions ?? [], true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'monthly_price', 'trial_days',
        'item_limit', 'staff_limit', 'customer_limit', 'features',
        'sort_order', 'is_active',
    ];

    /** Sections every plan has: you always need a dashboard, your staff and your website settings. */
    public const CORE = ['dashboard.view', 'users.manage', 'settings.manage'];

    /** Plan highlighted as "Recommended" on the plan choice page. */
    public const RECOMMENDED = 'professional';

    /** Plan features that aren't admin sections (key => label on the plan card). */
    public const EXTRA_FEATURES = ['website' => 'Restaurant website, online ordering & QR menu'];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'features'      => 'array',
    ];

    /** Does this plan unlock an admin section (a Role::PERMISSIONS key)? */
    public function hasFeature(string $feature): bool
    {
        return $this->features === null
            || in_array($feature, self::CORE, true)
            || in_array($feature, $this->features, true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hourlyRate(): float
    {
        return round(((float) $this->monthly_price) / (30 * 24), 4);
    }
}

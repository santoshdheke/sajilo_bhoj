<?php

namespace Modules\Restaurant\Models;

use Modules\Restaurant\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NavMenuItem extends Model
{
    use BelongsToCompany;

    protected $connection = 'restaurant';

    protected $fillable = ['company_id', 'label', 'url', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'      => 'boolean',
    ];

    /** Relative paths, so they work on whichever domain the restaurant uses (and need no routes at sign-up). */
    public static function defaults(): array
    {
        return [
            ['label' => 'Home',          'url' => '/',         'icon' => 'bi-house'],
            ['label' => 'About',         'url' => '/about', 'icon' => 'bi-info-circle'],
            ['label' => 'Menu',          'url' => '/menu',     'icon' => 'bi-grid'],
            ['label' => 'Book a Table',  'url' => '/table', 'icon' => 'bi-calendar-check'],
            ['label' => 'Contact',       'url' => '/contact', 'icon' => 'bi-envelope'],
        ];
    }

    public static function forCompany(int $companyId): Collection
    {
        $items = static::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->orderBy('sort_order')
            ->get();

        if ($items->isEmpty()) {
            foreach (static::defaults() as $i => $data) {
                static::create($data + ['company_id' => $companyId, 'sort_order' => $i]);
            }

            $items = static::withoutGlobalScope('company')
                ->where('company_id', $companyId)
                ->orderBy('sort_order')
                ->get();
        }

        return $items->where('is_active', true)->values();
    }
}

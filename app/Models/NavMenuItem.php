<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NavMenuItem extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'label', 'url', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'      => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            ['label' => 'Home',          'url' => route('frontend.home.index'),  'icon' => 'bi-house'],
            ['label' => 'About',         'url' => route('frontend.about.index'), 'icon' => 'bi-info-circle'],
            ['label' => 'Menu',          'url' => route('frontend.menu.index'),  'icon' => 'bi-grid'],
            ['label' => 'Book a Table',  'url' => route('frontend.table.index'), 'icon' => 'bi-calendar-check'],
            ['label' => 'Contact',       'url' => route('frontend.contact.index'), 'icon' => 'bi-envelope'],
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

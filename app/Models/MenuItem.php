<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = ['menu_category_id', 'name', 'description', 'price', 'preparation_time', 'is_available', 'sort_order'];

    protected $casts = ['is_available' => 'boolean', 'price' => 'float'];

    /**
     * Menu items have no branch column of their own: only resolve an item from a URL
     * when its category belongs to the current restaurant (categories are branch-scoped).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->whereHas('category')->first();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    /** Dishes have no photo of their own; they show their category's photo. */
    public function photoUrl(): ?string
    {
        return $this->category?->imageUrl();
    }
}

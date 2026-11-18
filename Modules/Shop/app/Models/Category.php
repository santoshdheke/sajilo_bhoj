<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** Product category; parent_id 0 = root (up to three levels). */
class Category extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'title', 'slug', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_category')->withTimestamps();
    }

    public function getParentNameAttribute(): string
    {
        return $this->parent_id && $this->parent ? $this->parent->title : '—';
    }

    /** Bootstrap icon for the storefront, picked from the category name (e.g. "Computers" → laptop). */
    public function getIconAttribute(): string
    {
        $map = [
            'bi-handbag'      => ['apparel', 'fashion', 'cloth', 'wear', 'shoe', 'bag', 'accessor'],
            'bi-car-front'    => ['auto', 'car', 'vehicle', 'bike', 'motor'],
            'bi-flower1'      => ['beauty', 'health', 'cosmetic', 'care', 'spa'],
            'bi-laptop'       => ['computer', 'laptop', 'pc'],
            'bi-book'         => ['book', 'learn', 'station', 'education'],
            'bi-briefcase'    => ['business', 'industr', 'office'],
            'bi-tv'           => ['electronic', 'tv', 'appliance'],
            'bi-phone'        => ['phone', 'mobile', 'gadget'],
            'bi-basket'       => ['grocer', 'food', 'vegetable', 'fruit', 'kitchen'],
            'bi-house-door'   => ['home', 'furniture', 'decor'],
            'bi-controller'   => ['game', 'toy', 'kid'],
            'bi-heart-pulse'  => ['medic', 'pharma'],
            'bi-trophy'       => ['sport', 'fitness'],
            'bi-gem'          => ['jewel', 'watch'],
        ];
        $title = strtolower($this->title);
        foreach ($map as $icon => $words) {
            foreach ($words as $word) {
                if (str_contains($title, $word)) {
                    return $icon;
                }
            }
        }

        return 'bi-tag';
    }

    /** Options for a parent <select>: roots and their children (a product category can be three levels deep). */
    public static function parentOptions(?int $exceptId = null): array
    {
        $options = [];
        foreach (static::where('parent_id', 0)->orderBy('title')->get() as $root) {
            if ($root->id === $exceptId) continue;
            $options[$root->id] = $root->title;
            foreach (static::where('parent_id', $root->id)->orderBy('title')->get() as $child) {
                if ($child->id === $exceptId) continue;
                $options[$child->id] = '— '.$child->title;
            }
        }

        return $options;
    }
}

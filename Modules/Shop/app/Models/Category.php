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

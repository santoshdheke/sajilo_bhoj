<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** A product section on the storefront home page (e.g. "New Arrivals"). */
class Section extends Model
{
    use BelongsToShop;

    public const TYPES = ['single_line' => 'Single line (scrolling row)', 'multiple_line' => 'Multiple lines (grid)'];

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'title', 'type', 'page', 'sort_order'];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn (Builder $q) => $q->orderBy('sort_order')->orderBy('id'));
    }

    public function sectionItems(): HasMany
    {
        // In the order they were added; the same product may appear more than once.
        return $this->hasMany(SectionItem::class)->orderBy('id');
    }

    /**
     * One product per item, in item order (duplicates kept, deleted products skipped).
     * $limit = null shows them all.
     */
    public function products(?int $limit = null): Collection
    {
        $items = $this->relationLoaded('sectionItems') ? $this->sectionItems : $this->sectionItems()->get();
        $items = $items->where('sectionable_type', Product::class)->values();

        $products = Product::with('productImage', 'category')->whereIn('id', $items->pluck('sectionable_id')->unique())->get()->keyBy('id');

        $list = $items->map(fn ($item) => $products->get($item->sectionable_id))->filter()->values();

        return $limit ? $list->take($limit) : $list;
    }
}

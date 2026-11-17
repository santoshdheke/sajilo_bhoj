<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** A product section on the storefront home page (e.g. "New Arrivals"). */
class Section extends Model
{
    use BelongsToShop;

    public const TYPES = ['single_line' => 'Grid (one block)', 'multiple_line' => 'Carousel row'];

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'title', 'type', 'page', 'sort_order'];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn (Builder $q) => $q->orderBy('sort_order')->orderBy('id'));
    }

    public function sectionItems(): HasMany
    {
        return $this->hasMany(SectionItem::class);
    }

    public function productIds(): array
    {
        return $this->sectionItems()->where('sectionable_type', Product::class)->pluck('sectionable_id')->all();
    }
}

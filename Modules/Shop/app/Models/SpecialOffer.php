<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** Special offer slide shown next to the home banner (theme "vegitable"). */
class SpecialOffer extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'title', 'sub_title', 'image', 'url', 'status', 'sort_order'];

    protected $casts = ['status' => 'boolean'];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn (Builder $q) => $q->orderBy('sort_order')->orderBy('id'));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', true);
    }

    public function getImagePathAttribute(): string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : '';
    }
}

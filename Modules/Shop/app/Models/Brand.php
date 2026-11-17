<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Modules\Shop\Models\Concerns\BelongsToShop;

class Brand extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'name', 'slug', 'logo', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}

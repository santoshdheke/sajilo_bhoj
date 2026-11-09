<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuCategory extends Model
{
    use BelongsToBranch;

    protected $fillable = ['branch_id', 'name', 'image', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order')->orderBy('name');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}

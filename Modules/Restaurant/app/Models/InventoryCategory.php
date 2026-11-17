<?php

namespace Modules\Restaurant\Models;

use Modules\Restaurant\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends Model
{
    use BelongsToBranch;

    protected $connection = 'restaurant';

    protected $fillable = ['branch_id', 'name'];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }
}

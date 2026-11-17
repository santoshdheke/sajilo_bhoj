<?php

namespace Modules\Restaurant\Models;

use Modules\Restaurant\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenColumn extends Model
{
    use BelongsToBranch;

    protected $connection = 'restaurant';

    protected $fillable = ['branch_id', 'name', 'slug', 'color', 'sort_order'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status', 'slug');
    }
}

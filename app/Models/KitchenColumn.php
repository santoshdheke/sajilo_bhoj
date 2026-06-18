<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenColumn extends Model
{
    use BelongsToBranch;

    protected $fillable = ['branch_id', 'name', 'slug', 'color', 'sort_order'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status', 'slug');
    }
}

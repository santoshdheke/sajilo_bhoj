<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use BelongsToBranch;

    protected $fillable = ['branch_id', 'name', 'photo', 'description'];

    public function floorTables(): HasMany
    {
        return $this->hasMany(FloorTable::class);
    }
}

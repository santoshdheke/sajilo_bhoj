<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorTablePhoto extends Model
{
    protected $fillable = ['table_id', 'path', 'sort_order'];

    public function floorTable(): BelongsTo
    {
        return $this->belongsTo(FloorTable::class, 'table_id');
    }
}

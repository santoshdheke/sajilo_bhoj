<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chair extends Model
{
    protected $fillable = ['table_id', 'side'];

    public function floorTable(): BelongsTo
    {
        return $this->belongsTo(FloorTable::class, 'table_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableBooking extends Model
{
    protected $fillable = [
        'table_id', 'name', 'phone', 'email',
        'booking_date', 'booking_time', 'party_size', 'notes', 'status',
    ];

    protected $casts = ['booking_date' => 'date'];

    public function floorTable(): BelongsTo
    {
        return $this->belongsTo(FloorTable::class, 'table_id');
    }

    public function getTimeFormattedAttribute(): string
    {
        return \Carbon\Carbon::parse($this->booking_time)->format('h:i A');
    }
}

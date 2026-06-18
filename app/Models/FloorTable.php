<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FloorTable extends Model
{
    use BelongsToBranch;

    protected $table = 'tables';

    protected $fillable = ['branch_id', 'room_id', 'table_no', 'name', 'capacity', 'qr_code', 'pos_x', 'pos_y', 'description', 'status'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function chairs(): HasMany
    {
        return $this->hasMany(Chair::class, 'table_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FloorTablePhoto::class, 'table_id')->orderBy('sort_order')->orderBy('id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TableBooking::class, 'table_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function todayBookings(): HasMany
    {
        return $this->hasMany(TableBooking::class, 'table_id')
                    ->whereDate('booking_date', today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('booking_time');
    }

    /**
     * The floor-plan UI predates the schema's `table_no` column and refers to
     * tables by "name" everywhere — alias it onto `table_no` so that code keeps working.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->table_no,
            set: fn ($value) => ['table_no' => $value],
        );
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'occupied'  => 'danger',
            'reserved'  => 'warning',
            'cleaning'  => 'secondary',
            default     => 'secondary',
        };
    }
}

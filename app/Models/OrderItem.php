<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_item_id', 'name', 'quantity', 'price', 'subtotal', 'note'];

    protected $casts = ['price' => 'float', 'subtotal' => 'float'];

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item) {
            $item->subtotal = round($item->quantity * $item->price, 2);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->subtotal;
    }
}

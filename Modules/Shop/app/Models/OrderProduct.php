<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['order_id', 'product_id', 'unit_price', 'attribute_description', 'quantity'];

    protected $casts = ['attribute_description' => 'array', 'unit_price' => 'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function getLineTotalAttribute(): float { return (float) $this->unit_price * $this->quantity; }
}

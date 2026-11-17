<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $connection = 'restaurant';

    protected $fillable = ['category_id', 'name', 'unit', 'quantity', 'reorder_level', 'cost_price'];

    protected $casts = [
        'quantity'      => 'float',
        'reorder_level' => 'float',
        'cost_price'    => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}

<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['purchase_order_id', 'product_id', 'unit_price', 'quantity', 'tax_rate', 'total'];

    protected $casts = ['unit_price' => 'decimal:2', 'total' => 'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
}

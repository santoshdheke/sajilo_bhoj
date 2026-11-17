<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** Stock purchase from a supplier; its items count as stock in. */
class PurchaseOrder extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'supplier_id', 'po_number', 'subtotal', 'tax_amount', 'discount', 'total', 'paid_amount', 'status', 'payment_status', 'order_date', 'expected_date', 'notes'];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
        'subtotal'      => 'decimal:2',
        'tax_amount'    => 'decimal:2',
        'discount'      => 'decimal:2',
        'total'         => 'decimal:2',
        'paid_amount'   => 'decimal:2',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }

    public function getDueAmountAttribute(): float { return max(0, (float) $this->total - (float) $this->paid_amount); }
}

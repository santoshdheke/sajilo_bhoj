<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use BelongsToBranch;

    protected $fillable = ['supplier_id', 'branch_id', 'invoice_no', 'total_amount', 'status'];

    protected $casts = ['total_amount' => 'float'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function recalcTotal(): void
    {
        $this->update(['total_amount' => $this->items()->sum('subtotal')]);
    }
}

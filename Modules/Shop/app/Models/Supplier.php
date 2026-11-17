<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Shop\Models\Concerns\BelongsToShop;

class Supplier extends Model
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'name', 'phone', 'email', 'address', 'company_name', 'pan_vat', 'balance', 'notes'];

    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }

    public function getTotalPurchasesAttribute(): float { return (float) $this->purchaseOrders()->sum('total'); }
    public function getTotalPaidAttribute(): float { return (float) $this->purchaseOrders()->sum('paid_amount'); }
    public function getOutstandingAttribute(): float { return $this->total_purchases - $this->total_paid; }
}

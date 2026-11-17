<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Ledger of a customer's balance: credit sales (negative) and payments received (positive). */
class CustomerTransaction extends Model
{
    protected $connection = 'shop';

    protected $fillable = ['customer_id', 'order_id', 'type', 'amount', 'balance_after', 'description', 'reference'];

    protected $casts = ['amount' => 'decimal:2', 'balance_after' => 'decimal:2'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}

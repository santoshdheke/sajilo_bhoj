<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use BelongsToBranch, BelongsToCompany;

    protected $fillable = [
        'company_id', 'branch_id', 'table_id', 'customer_id', 'order_no',
        'customer_name', 'order_type', 'subtotal', 'discount', 'tax',
        'grand_total', 'status', 'created_by', 'notes',
    ];

    protected $casts = [
        'subtotal'    => 'float',
        'discount'    => 'float',
        'tax'         => 'float',
        'grand_total' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->order_no) {
                $order->order_no = 'ORD-' . str_pad((string) (static::withoutGlobalScopes()->max('id') + 1), 4, '0', STR_PAD_LEFT);
            }

            if (! $order->created_by && auth()->check()) {
                $order->created_by = auth()->id();
            }
        });
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(FloorTable::class, 'table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function kitchenTicket(): HasOne
    {
        return $this->hasOne(KitchenTicket::class);
    }

    public function recalcTotals(): void
    {
        $subtotal = (float) $this->items()->sum(DB::raw('quantity * price'));
        $grandTotal = max(0, $subtotal - $this->discount + $this->tax);

        $this->update(['subtotal' => $subtotal, 'grand_total' => $grandTotal]);
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match ($this->order_type) {
            'dine_in'  => 'Dine In',
            'takeaway' => 'Takeaway',
            'delivery' => 'Delivery',
            default    => ucfirst($this->order_type),
        };
    }
}

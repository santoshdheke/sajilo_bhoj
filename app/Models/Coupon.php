<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'code', 'discount_type', 'discount_value', 'valid_from', 'valid_to'];

    protected $casts = [
        'discount_value' => 'float',
        'valid_from'     => 'datetime',
        'valid_to'       => 'datetime',
    ];

    public function isValidNow(): bool
    {
        $now = now();

        return (! $this->valid_from || $now->gte($this->valid_from))
            && (! $this->valid_to || $now->lte($this->valid_to));
    }

    public function discountFor(float $subtotal): float
    {
        return $this->discount_type === 'percent'
            ? round($subtotal * $this->discount_value / 100, 2)
            : min($this->discount_value, $subtotal);
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TopupPayment extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'user_id', 'gateway', 'amount', 'status', 'reference', 'gateway_token', 'gateway_txn_id', 'response', 'completed_at'];

    protected $casts = ['amount' => 'decimal:2', 'response' => 'array', 'completed_at' => 'datetime'];

    public function gatewayName(): string
    {
        return $this->gateway === 'esewa' ? 'eSewa' : 'Khalti';
    }
}

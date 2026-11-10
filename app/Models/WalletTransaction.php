<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'type', 'amount', 'balance_after', 'note'];

    protected $casts = ['amount' => 'decimal:2', 'balance_after' => 'decimal:2'];
}

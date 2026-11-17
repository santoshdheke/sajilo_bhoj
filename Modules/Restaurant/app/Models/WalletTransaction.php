<?php

namespace Modules\Restaurant\Models;

use Modules\Restaurant\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use BelongsToCompany;

    protected $connection = 'mysql';

    protected $fillable = ['company_id', 'type', 'amount', 'balance_after', 'note'];

    protected $casts = ['amount' => 'decimal:2', 'balance_after' => 'decimal:2'];
}

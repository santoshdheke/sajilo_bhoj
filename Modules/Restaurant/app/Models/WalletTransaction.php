<?php

namespace Modules\Restaurant\Models;

use Modules\Restaurant\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use BelongsToCompany;

    protected $connection = 'mysql';

    protected $fillable = ['company_id', 'type', 'source_company_id', 'amount', 'balance_after', 'note'];

    protected $casts = ['amount' => 'decimal:2', 'balance_after' => 'decimal:2'];

    /** For a referral bonus: the client company whose top-up earned it. */
    public function source(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class, 'source_company_id');
    }
}

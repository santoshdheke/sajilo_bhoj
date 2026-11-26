<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Restaurant\Models\Concerns\BelongsToCompany;

/** One change to a company's Refer & Earn earnings wallet (see Support\ReferralEarnings). */
class EarningTransaction extends Model
{
    use BelongsToCompany;

    protected $connection = 'mysql';

    protected $fillable = ['company_id', 'type', 'amount', 'earnings_after', 'source_company_id', 'withdrawal_request_id', 'note'];

    protected $casts = ['amount' => 'decimal:2', 'earnings_after' => 'decimal:2'];

    /** For a bonus: the client company whose top-up earned it. */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'source_company_id');
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class, 'withdrawal_request_id');
    }
}

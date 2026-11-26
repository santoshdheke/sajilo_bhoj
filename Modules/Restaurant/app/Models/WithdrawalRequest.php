<?php

namespace Modules\Restaurant\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Restaurant\Models\Concerns\BelongsToCompany;

/** A request to pay out Refer & Earn earnings; paid or rejected by the SajiloBhoj team. */
class WithdrawalRequest extends Model
{
    use BelongsToCompany;

    public const METHODS = ['esewa' => 'eSewa', 'khalti' => 'Khalti', 'bank' => 'Bank transfer'];

    protected $connection = 'mysql';

    protected $fillable = ['company_id', 'user_id', 'amount', 'method', 'account_name', 'account_number', 'bank_name',
        'status', 'payout_reference', 'admin_note', 'processed_at'];

    protected $casts = ['amount' => 'decimal:2', 'processed_at' => 'datetime'];

    public function methodName(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    /** "eSewa · 98XXXXXXXX" / "Nabil Bank · 0123…" */
    public function destination(): string
    {
        return ($this->method === 'bank' ? ($this->bank_name ?: 'Bank') : $this->methodName()) . ' · ' . $this->account_number;
    }
}

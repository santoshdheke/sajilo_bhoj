<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'plan_id', 'status', 'trial_ends_at', 'starts_at', 'ends_at', 'last_charged_at'];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'last_charged_at' => 'datetime',
    ];

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }

    /** Whole days of trial left, counting today (e.g. 22.3 days -> 23). */
    public function trialDaysLeft(): int
    {
        return $this->isTrialing() ? (int) ceil(now()->diffInMinutes($this->trial_ends_at) / 1440) : 0;
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}

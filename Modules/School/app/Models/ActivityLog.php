<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** Business/audit trail of a school (sms's activity_logs), written only by ActivityLogService. */
class ActivityLog extends Model
{
    protected $connection = 'school';

    protected $fillable = [
        'company_id',
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /** Who did it: a school User, or null for anonymous events such as a failed login. */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}

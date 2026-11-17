<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A host name pointing at a company's workspace (common database). */
class Domain extends Model
{
    protected $connection = 'mysql';

    protected $table = 'domains';

    protected $fillable = ['company_id', 'domain', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

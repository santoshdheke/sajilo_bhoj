<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'key', 'value'];

    protected $casts = ['value' => 'array'];

    public static function getValue(int $companyId, string $key, mixed $default = null): mixed
    {
        return static::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function putValue(int $companyId, string $key, mixed $value): self
    {
        return static::withoutGlobalScope('company')->updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            ['value' => $value]
        );
    }
}

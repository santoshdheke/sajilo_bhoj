<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A workspace (tenant), stored in the common database. `company_type` says which
 * module runs it; each module extends this model with its own relations
 * (Modules\Restaurant\Models\Company, Modules\Shop\Models\Company,
 * Modules\School\Models\Company).
 */
class Company extends Model
{
    public const TYPE_RESTAURANT = 'restaurant';
    public const TYPE_SHOP = 'shop';
    public const TYPE_SCHOOL = 'school';
    public const TYPES = [self::TYPE_RESTAURANT, self::TYPE_SHOP, self::TYPE_SCHOOL];

    protected $connection = 'mysql';

    protected $table = 'companies';

    protected $fillable = ['name', 'slug', 'company_type', 'referred_by_company_id', 'email', 'phone', 'address', 'logo', 'status'];

    protected $casts = ['status' => 'boolean', 'balance' => 'decimal:2'];

    /**
     * Per company type: how to tell that a sign-up is finished (e.g. the owner verified
     * their email). Registered by each module; a type without one counts as verified.
     *
     * @var array<string, Closure(self): bool>
     */
    protected static array $verifiers = [];

    /** Per-request cache for isVerified() (a plain property, never saved). */
    private ?bool $verified = null;

    public static function verifyUsing(string $type, Closure $verifier): void
    {
        static::$verifiers[$type] = $verifier;
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'company_id');
    }

    /** A workspace is live once its sign-up is finished. Looked up once per request. */
    public function isVerified(): bool
    {
        $verifier = static::$verifiers[$this->company_type] ?? null;

        return $this->verified ??= $verifier ? (bool) $verifier($this) : true;
    }
}

<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Shop\Models\Concerns\BelongsToShop;

/**
 * balance < 0 means the customer owes the shop (udhar / credit).
 * Online shoppers sign in with their customer record (guard "shop_customer"); counter (POS)
 * customers have no password.
 */
class Customer extends Authenticatable
{
    use BelongsToShop;

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'first_name', 'last_name', 'email', 'phone_number', 'address_one', 'address_two', 'balance', 'loyalty_points', 'notes', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['balance' => 'decimal:2', 'password' => 'hashed'];

    public function transactions(): HasMany { return $this->hasMany(CustomerTransaction::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }

    public function getFullNameAttribute(): string { return trim($this->first_name.' '.$this->last_name); }
    public function getIsInDebtAttribute(): bool { return $this->balance < 0; }
    // The storefront theme reads name / address.
    public function getNameAttribute(): string { return $this->full_name; }
    public function getAddressAttribute(): ?string { return $this->address_one; }

    public function getUdharAmountAttribute(): float { return abs(min(0, (float) $this->balance)); }
}

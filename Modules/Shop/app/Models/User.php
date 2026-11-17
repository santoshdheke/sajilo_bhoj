<?php

namespace Modules\Shop\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Modules\Shop\Models\Concerns\BelongsToShop;

/** Shop owner / staff account, guard "shop". */
class User extends Authenticatable
{
    use BelongsToShop, Notifiable;

    public const ROLE_OWNER = 'owner';
    public const ROLE_STAFF = 'admin';

    protected $connection = 'shop';

    protected $fillable = [
        'company_id', 'name', 'email', 'phone_number', 'address', 'profile_picture',
        'password', 'role', 'status', 'invite_token', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token', 'invite_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function getInitialsAttribute(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))->filter()->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->isOwner() ? 'Owner' : 'Staff';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->profile_picture ? Storage::disk('public')->url($this->profile_picture) : null;
    }
}

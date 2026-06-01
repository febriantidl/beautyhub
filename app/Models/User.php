<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'phone', 'avatar',
        'address', 'gender', 'is_active',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
        'password'  => 'hashed',
    ];

    // ── JWT ───────────────────────────────────────────────────────
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims(): array
    {
        return ['role' => $this->role, 'name' => $this->name];
    }

    // ── Relasi ────────────────────────────────────────────────────
    public function mua()      { return $this->hasOne(Mua::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    // ── Role helpers ──
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isMua(): bool
{
    return $this->role === 'mua';
}

public function isCustomer(): bool
{
    return $this->role === 'customer';
}
}


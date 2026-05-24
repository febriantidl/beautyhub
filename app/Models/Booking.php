<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'mua_id', 'service_id', 'booking_date', 'event_date', 
        'time_slot', 'location', 'location_notes', 'price', 'notes', 
        'reference_image', 'status', 'verification_code', 'qr_code_path', 
        'rejection_reason', 'verified', 'verified_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'event_date'   => 'date',
        'verified_at'  => 'datetime',
        'price'        => 'integer',
        'verified'     => 'boolean',
    ];

    // ─── Status Constants ──────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed'; 
    const STATUS_VERIFIED = 'verified';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // ─── Relasi ───────────────────────────────────────────────────────
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function mua(): BelongsTo { return $this->belongsTo(Mua::class); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function review(): HasOne { return $this->hasOne(Review::class); }

    // ─── Helpers ──────────────────────────────────────────────────────
    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isConfirmed(): bool { return $this->status === self::STATUS_CONFIRMED; }
    public function isCompleted(): bool { return $this->status === self::STATUS_COMPLETED; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }
}
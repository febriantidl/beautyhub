<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',

        'user_id',
        'mua_id',
        'service_id',

        'booking_date',
        'event_date',
        'time_slot',

        'location_address',
        'location_notes',

        'price',
        'notes',
        'reference_image',

        'status',

        'verification_code',
        'qr_code_path',

        'rejection_reason',

        'verified',
        'verified_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'event_date'  => 'date',
        'verified_at' => 'datetime',
        'verified'    => 'boolean',
        'price'       => 'integer',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_VERIFIED  = 'verified';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mua(): BelongsTo
    {
        return $this->belongsTo(Mua::class, 'mua_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED || $this->verified === true;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
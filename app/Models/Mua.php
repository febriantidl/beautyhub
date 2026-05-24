<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mua extends Model
{
    protected $table = 'muas';

    protected $fillable = [
        'user_id',
        'location',
        'bio',
        'experience_years',
        'rating',
        'total_reviews',
        'style_tags',
        'certificate',
        'is_verified',
    ];

    protected $casts = [
        'style_tags'       => 'array',
        'is_verified'      => 'boolean',
        'rating'           => 'float',
        'experience_years' => 'integer',
        'total_reviews'    => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ─── Computed Helpers ────────────────────────────────────────────
    public function recalculateRating(): void
    {
        $avg = $this->reviews()->avg('rating') ?? 0;
        $count = $this->reviews()->count();
        $this->update([
            'rating'        => round($avg, 2),
            'total_reviews' => $count,
        ]);
    }
}
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
        'style_tags' => 'array',
        'is_verified' => 'boolean',
        'rating' => 'float',
    ];

    // Relasi balik ke model User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Hubungkan ke model Portfolio (Ini yang tadi bikin error)
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
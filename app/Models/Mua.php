<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mua extends Model
{
    // Menentukan nama tabel (opsional, tapi bagus buat mastiin Laravel ga salah baca jadi muas)
    protected $table = 'muas';

    // Daftar kolom yang wajib masuk fillable biar bisa di-create otomatis
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

    // Karena di tabel kamu ada kolom bertipe JSON, kita cast biar otomatis jadi array di PHP
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
}
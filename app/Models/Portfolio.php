<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    protected $fillable = [
        'mua_id',
        'image_path',
        'title',
        'caption',
        'feature_vector',
        'style_category',
    ];

    protected $casts = [
        'feature_vector' => 'array',
    ];

    public function mua(): BelongsTo
    {
        return $this->belongsTo(Mua::class);
    }
}

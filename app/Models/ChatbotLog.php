<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_message',
        'detected_intent',
        'bot_response',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

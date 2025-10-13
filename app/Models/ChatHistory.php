<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatHistory extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'user_message',
        'bot_response',
        'suggested_products'
    ];

    protected $casts = [
        'suggested_products' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getSessionHistory(string $sessionId, int $limit = 20)
    {
        return self::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public static function clearSessionHistory(string $sessionId, ?int $userId = null)
    {
        $query = self::where('session_id', $sessionId);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->delete();
    }
}

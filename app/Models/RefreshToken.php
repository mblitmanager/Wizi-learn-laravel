<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RefreshToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'device_name',
        'ip_address',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the refresh token
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark the token as used
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Clean expired refresh tokens
     */
    public static function cleanExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }

    /**
     * Revoke all tokens for a user
     */
    public static function revokeAllForUser(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }
}

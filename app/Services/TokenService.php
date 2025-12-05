<?php

namespace App\Services;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    /**
     * Create access and refresh tokens for a user
     */
    public function createTokens(User $user, ?string $deviceName = null, ?string $ipAddress = null): array
    {
        // Generate JWT access token
        $accessToken = JWTAuth::fromUser($user);
        
        // Generate refresh token
        $refreshToken = $this->createRefreshToken($user, $deviceName, $ipAddress);
        
        // Get TTL in seconds
        $ttl = config('jwt.ttl', 60) * 60;
        
        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken->token,
            'expires_in' => $ttl,
            'token_type' => 'bearer',
        ];
    }

    /**
     * Create a refresh token for the user
     */
    private function createRefreshToken(User $user, ?string $deviceName, ?string $ipAddress): RefreshToken
    {
        // Get refresh TTL from config (default 14 days)
        $refreshTtl = config('jwt.refresh_ttl', 20160);
        
        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addMinutes($refreshTtl),
            'device_name' => $deviceName,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(string $refreshTokenString): array
    {
        $refreshToken = RefreshToken::where('token', $refreshTokenString)->first();

        if (!$refreshToken) {
            throw new \Exception('Invalid refresh token');
        }

        if ($refreshToken->isExpired()) {
            $refreshToken->delete();
            throw new \Exception('Refresh token has expired');
        }

        // Mark as used
        $refreshToken->markAsUsed();

        // Generate new access token
        $accessToken = JWTAuth::fromUser($refreshToken->user);

        // Get TTL
        $ttl = config('jwt.ttl', 60) * 60;

        return [
            'access_token' => $accessToken,
            'expires_in' => $ttl,
            'token_type' => 'bearer',
        ];
    }

    /**
     * Revoke all refresh tokens for a user
     */
    public function revokeAllTokens(User $user): int
    {
        return RefreshToken::revokeAllForUser($user->id);
    }

    /**
     * Revoke specific refresh token
     */
    public function revokeToken(string $refreshTokenString): bool
    {
        return RefreshToken::where('token', $refreshTokenString)->delete() > 0;
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        return RefreshToken::cleanExpired();
    }
}

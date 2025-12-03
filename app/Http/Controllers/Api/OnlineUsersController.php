<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis as RedisClient;

class OnlineUsersController extends Controller
{
    /**
     * Get list of currently online users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get all online user IDs from Redis
            $redis = RedisClient::connection();
            $onlineUserKeys = $redis->keys('user:online:*');

            $onlineUserIds = [];
            $userDurations = [];

            foreach ($onlineUserKeys as $key) {
                // Extract user ID from key (format: user:online:123)
                $userId = str_replace('user:online:', '', $key);
                $onlineUserIds[] = $userId;

                // Get online timestamp
                $timestamp = $redis->get($key);
                if ($timestamp) {
                    $onlineSince = \Carbon\Carbon::createFromTimestamp($timestamp);
                    $duration = now()->diffInMinutes($onlineSince);
                    $userDurations[$userId] = $duration;
                }
            }

            // Get user details
            $users = User::whereIn('id', $onlineUserIds)
                ->select('id', 'name', 'role', 'image')
                ->get()
                ->map(function ($user) use ($userDurations) {
                    return [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'onlineDuration' => $userDurations[$user->id] ?? 0,
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            // Fallback if Redis is not available
            // Return users with recent activity (last 5 minutes)
            $users = User::where('last_activity_at', '>=', now()->subMinutes(5))
                ->select('id', 'name', 'role', 'image')
                ->get()
                ->map(function ($user) {
                    $duration = $user->last_activity_at 
                        ? now()->diffInMinutes($user->last_activity_at) 
                        : 0;

                    return [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'onlineDuration' => $duration,
                    ];
                });

            return response()->json($users);
        }
    }
}

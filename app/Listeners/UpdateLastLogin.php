<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        try {
            $ip = request()->header('X-Forwarded-For') ?? request()->ip();

            $event->user->last_login_at = now();
            $event->user->last_activity_at = now();
            $event->user->last_login_ip = $ip;
            $event->user->is_online = true;
            $event->user->save();

            Log::info('User logged in', [
                'user_id' => $event->user->id,
                'ip' => $ip,
                'time' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Login update failed', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? 'unknown'
            ]);
        }
    }
}

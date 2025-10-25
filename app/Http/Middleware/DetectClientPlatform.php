<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\UserClientSession;

class DetectClientPlatform
{
    /**
     * Handle an incoming request.
     *
     * Detects the client platform from header X-Client-Type or User-Agent
     * and sets authenticated user's `last_client` field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $client = null;

        // Prefer explicit header set by frontend/mobile app
        if ($request->headers->has('X-Client-Type')) {
            $client = strtolower($request->header('X-Client-Type'));
        }

        // Fallback: inspect user agent for mobile indicators
        if (!$client) {
            $ua = strtolower($request->userAgent() ?? '');
            if (Str::contains($ua, ['android'])) {
                $client = 'android';
            } elseif (Str::contains($ua, ['iphone', 'ipad', 'ipod', 'ios'])) {
                $client = 'ios';
            } elseif (Str::contains($ua, ['mobile'])) {
                $client = 'mobile';
            } else {
                $client = 'web';
            }
        }

        // If user is authenticated via auth guard, update last_client and optional session
        try {
            $user = $request->user();
            if ($user) {
                // Only update if changed to avoid frequent writes
                if (($user->last_client ?? null) !== $client) {
                    $user->last_client = $client;
                    $user->saveQuietly();
                }

                // Optional: upsert a client session when device info provided
                $deviceId = $request->header('X-Device-Id');
                $appVersion = $request->header('X-App-Version');
                if ($deviceId) {
                    try {
                        $ip = $request->ip();
                        $platform = $client;
                        $now = now();

                        $session = UserClientSession::firstOrNew([
                            'user_id' => $user->id,
                            'device_id' => $deviceId,
                        ]);

                        $session->platform = $platform;
                        $session->app_version = $appVersion;
                        $session->ip = $ip;
                        $session->last_seen_at = $now;
                        $session->saveQuietly();
                    } catch (\Exception $e) {
                        // swallow session errors
                    }
                }
            }
        } catch (\Exception $e) {
            // Don't block request on errors updating user/session
        }

        // Expose detected client for controllers
        $request->attributes->set('detected_client', $client);

        return $next($request);
    }
}

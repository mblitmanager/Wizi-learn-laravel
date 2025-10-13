<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAppUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAppUsageController extends Controller
{
    public function report(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|in:android,ios',
            'app_version' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:150',
            'os_version' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();

        $usage = UserAppUsage::firstOrNew([
            'user_id' => $user->id,
            'platform' => $validated['platform'],
        ]);

        if (!$usage->exists || empty($usage->first_used_at)) {
            $usage->first_used_at = now();
        }

        $usage->last_used_at = now();
        $usage->app_version = $validated['app_version'] ?? $usage->app_version;
        $usage->device_model = $validated['device_model'] ?? $usage->device_model;
        $usage->os_version = $validated['os_version'] ?? $usage->os_version;
        $usage->user_id = $user->id;
        $usage->save();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'platform' => $usage->platform,
                'first_used_at' => optional($usage->first_used_at)->toISOString(),
                'last_used_at' => optional($usage->last_used_at)->toISOString(),
                'app_version' => $usage->app_version,
                'device_model' => $usage->device_model,
                'os_version' => $usage->os_version,
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class UserSettingsController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json([], 401);

        $settings = UserSetting::where('user_id', $user->id)->get()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json([], 401);

        $data = $request->all();
        foreach ($data as $key => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['ok' => true]);
    }
}

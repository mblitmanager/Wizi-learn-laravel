<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\User;

class TestFcmController extends Controller
{
    public function send(Request $request, NotificationService $notificationService)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'token' => 'nullable|string',
            'user_id' => 'nullable|integer'
        ]);

        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []);

        // If a token is provided, build a temporary object that carries fcm_token
        if ($request->filled('token')) {
            $tmpUser = new \stdClass();
            $tmpUser->fcm_token = $request->input('token');
            $sent = $notificationService->sendFcmToUser($tmpUser, $title, $body, $data);
            return response()->json(['ok' => (bool) $sent]);
        }

        // If user_id provided, try to send to that user
        if ($request->filled('user_id')) {
            $user = User::find($request->input('user_id'));
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $sent = $notificationService->sendFcmToUser($user, $title, $body, $data);
            return response()->json(['ok' => (bool) $sent]);
        }

        return response()->json(['error' => 'Provide token or user_id'], 422);
    }
}

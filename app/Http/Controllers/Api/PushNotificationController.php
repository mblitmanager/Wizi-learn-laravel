<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotificationJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    /**
     * Send push notification to user segment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'segment' => 'required|string|in:all,commercial,formateur,stagiaire,admin',
            'message' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get users based on segment
        $usersQuery = User::whereNotNull('fcm_token');

        if ($request->segment !== 'all') {
            $usersQuery->where('role', $request->segment);
        }

        $users = $usersQuery->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé pour ce segment'
            ], 404);
        }

        // Dispatch job for async processing
        SendPushNotificationJob::dispatch(
            $users,
            $request->message,
            Auth::id()
        );

        // Store notification history
        \DB::table('notification_history')->insert([
            'type' => 'push',
            'subject' => null,
            'message' => $request->message,
            'segment' => $request->segment,
            'recipient_count' => $users->count(),
            'status' => 'pending',
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification en cours d\'envoi',
            'recipient_count' => $users->count()
        ]);
    }
}

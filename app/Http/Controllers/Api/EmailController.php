<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    /**
     * Send bulk email to multiple users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'userIds' => 'required|array|min:1',
            'userIds.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get users
        $users = User::whereIn('id', $request->userIds)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé'
            ], 404);
        }

        // Dispatch job for async processing
        SendBulkEmailJob::dispatch(
            $users,
            $request->subject,
            $request->message,
            Auth::id()
        );

        // Store notification history
        \DB::table('notification_history')->insert([
            'type' => 'email',
            'subject' => $request->subject,
            'message' => $request->message,
            'segment' => 'custom',
            'recipient_count' => $users->count(),
            'status' => 'pending',
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email en cours d\'envoi',
            'recipient_count' => $users->count()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationHistoryController extends Controller
{
    /**
     * Get notification history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // all, email, push
        $perPage = $request->get('perPage', 10);

        $query = DB::table('notification_history')
            ->select([
                'id',
                'type',
                'subject',
                'message',
                'segment',
                'recipient_count',
                'status',
                'created_by',
                'created_at'
            ])
            ->orderBy('created_at', 'desc');

        // Filter by type if provided
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // Get paginated results
        $notifications = $query->paginate($perPage);

        // Transform data
        $notifications->getCollection()->transform(function ($item) {
            return [
                'id' => (string) $item->id,
                'type' => $item->type,
                'subject' => $item->subject,
                'message' => $item->message,
                'segment' => $item->segment,
                'recipientCount' => $item->recipient_count,
                'status' => $item->status,
                'sentAt' => $item->created_at,
            ];
        });

        return response()->json($notifications);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Services\NotificationService;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,creators,subscribers',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_audience' => $request->target_audience,
            'created_by' => Auth::id() ?? 1, // Fallback for testing if not auth
            'status' => 'sent', // For now immediate send
            'sent_at' => now(),
        ]);

        // Send Notifications
        $query = User::query();

        if ($request->target_audience === 'creators') {
            $query->whereIn('role', ['formateur', 'formatrice']);
        } elseif ($request->target_audience === 'subscribers') {
            $query->where('role', 'stagiaire');
        }
        // 'all' doesn't need a filter

        $users = $query->whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            SendFcmNotificationJob::dispatch(
                $user,
                $announcement->title,
                $announcement->message,
                ['type' => 'announcement', 'announcement_id' => $announcement->id]
            );
        }

        return response()->json([
            'message' => 'Announcement created and sending started.',
            'announcement' => $announcement,
            'recipients_count' => $users->count()
        ], 201);
    }
}

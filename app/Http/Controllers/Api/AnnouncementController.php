<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Models\Stagiaire;
use App\Services\NotificationService;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Users see announcements created by them
        // Or if Admin, see all? For now, let's show history of what THEY sent.
        $query = Announcement::with('creator')->orderBy('created_at', 'desc');

        if ($user->role !== 'admin') {
           $query->where('created_by', $user->id);
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,stagiaires,formateurs,autres,specific_users',
            'recipient_ids' => 'nullable|array|required_if:target_audience,specific_users',
            'recipient_ids.*' => 'integer|exists:users,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();
        $targetAudience = $request->target_audience;
        $scheduledAt = $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null;

        // --- AUTHORIZATION & SCOPING ---
        if ($user->role === 'formateur' || $user->role === 'formatrice') {
            if (!in_array($targetAudience, ['stagiaires', 'specific_users'])) {
                return response()->json(['error' => 'Formateurs can only send to Stagiaires or Specific Users.'], 403);
            }
        } elseif ($user->role === 'commercial') {
             if (!in_array($targetAudience, ['stagiaires', 'specific_users'])) {
                return response()->json(['error' => 'Commercials can only send to Stagiaires or Specific Users.'], 403);
            }
        }

        // --- FETCH RECIPIENTS (Only validation needed here if scheduled, real fetch can happen later or now) ---
        if ($targetAudience === 'specific_users') {
             // ... scoped validation logic ...
              $allowedUsers = $this->getScopedStagiaireUsers($user);
              $requestedIds = $request->recipient_ids;
               if ($user->role !== 'admin') {
                   $allowedIds = $allowedUsers->pluck('id')->toArray();
                   $validIds = array_intersect($requestedIds, $allowedIds);
                   // Reset validIds to be array of integers
                   $filteredIds = array_values($validIds);
               } else {
                   $filteredIds = $requestedIds;
               }
        } else {
            $filteredIds = null;
        }

        // --- CREATE ANNOUNCEMENT ---
        $status = $scheduledAt ? 'scheduled' : 'sent';
        $sentAt = $scheduledAt ? null : now();

        $announcement = Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_audience' => $targetAudience,
            'recipient_ids' => $filteredIds,
            'created_by' => $user->id,
            'status' => $status, 
            'sent_at' => $sentAt,
            'scheduled_at' => $scheduledAt,
        ]);
        
        // If scheduled, stop here (after handling recipient storage - see next step)
        if ($scheduledAt) {
             return response()->json([
                'message' => 'Announcement scheduled successfully.',
                'announcement' => $announcement,
            ], 201);
        }

        // --- FETCH RECIPIENTS FOR IMMEDIATE SEND ---
        $recipients = collect();

        if ($targetAudience === 'all') {
            if ($user->role === 'admin') {
                $recipients = User::whereNotNull('fcm_token')->get();
            }
        } elseif ($targetAudience === 'formateurs') {
             if ($user->role === 'admin') {
                $recipients = User::whereIn('role', ['formateur', 'formatrice'])->whereNotNull('fcm_token')->get();
             }
        } elseif ($targetAudience === 'autres') {
             if ($user->role === 'admin') {
                $recipients = User::whereIn('role', ['commercial', 'admin'])->whereNotNull('fcm_token')->get();
             }
        } elseif ($targetAudience === 'stagiaires') {
            $recipients = $this->getScopedStagiaireUsers($user);
        } elseif ($targetAudience === 'specific_users') {
             // ... same logic as before ...
             // We can use the filteredIds we just saved
             if (empty($filteredIds)) {
                 $recipients = collect();
             } else {
                 $recipients = User::whereIn('id', $filteredIds)->whereNotNull('fcm_token')->get();
             }
        }

        // --- DISPATCH JOBS ---
        foreach ($recipients as $recipient) {
            SendFcmNotificationJob::dispatch(
                $recipient,
                $announcement->title,
                $announcement->message,
                ['type' => 'announcement', 'announcement_id' => $announcement->id]
            );
        }

        return response()->json([
            'message' => 'Announcement created and sending started.',
            'announcement' => $announcement,
            'recipients_count' => $recipients->count()
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     * Cancels scheduled announcements or deletes history.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['error' => 'Announcement not found.'], 404);
        }

        // Authorization: Creator or Admin
        if ($announcement->created_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully.']);
    }

    /**
     * Helper to get allowed stagiaire USERS for the current user.
     * Returns a Collection of User models (with FcmToken whereNotNull checked later usually, but here we return all valid users).
     */
    private function getScopedStagiaireUsers($sender)
    {
        if ($sender->role === 'admin') {
            return User::where('role', 'stagiaire')->with(['stagiaire.formations'])->get();
        }

        if ($sender->role === 'formateur' || $sender->role === 'formatrice') {
            $formateur = Formateur::where('user_id', $sender->id)->first();
            if (!$formateur) return collect();

            return User::with(['stagiaire.formations'])
                ->whereHas('stagiaire', function($q) use ($formateur) {
                    $q->whereHas('formateurs', function($formateurQ) use ($formateur) {
                        $formateurQ->where('formateurs.id', $formateur->id);
                    });
                })->get();
        }

        if ($sender->role === 'commercial') {
            $commercial = Commercial::where('user_id', $sender->id)->first();
            if (!$commercial) return collect();
             
             return User::with(['stagiaire.formations'])
                ->whereHas('stagiaire', function($q) use ($commercial) {
                    // Assuming relation name is 'commercials' in Stagiaire model as verified in Model file
                    $q->whereHas('commercials', function($commQ) use ($commercial) {
                        $commQ->where('commercials.id', $commercial->id);
                    });
                })->get();
        }

        return collect();
    }

    /**
     * Endpoint to get potential recipients for the frontend selector.
     */
    public function getRecipients()
    {
        $user = Auth::user();
        $recipients = collect();

        if ($user->role === 'admin') {
            $recipients = User::select('id', 'name', 'email', 'role')->get();
        } else {
            // Formateur / Commercial -> Only their stagiaires
            $recipients = $this->getScopedStagiaireUsers($user)->map(function($u) {
                // Ensure stagiaire relation is loaded to get formations
                $formationIds = [];
                if ($u->stagiaire) {
                    $formationIds = $u->stagiaire->formations->pluck('id')->toArray();
                }
                
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'is_online' => $u->is_online,
                    'last_login_at' => $u->last_login_at,
                    'last_activity_at' => $u->last_activity_at,
                    'formation_ids' => $formationIds
                ];
            });
        }

        return response()->json($recipients);
    }
}

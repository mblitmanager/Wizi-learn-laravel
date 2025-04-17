<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use App\Services\FormationService;
use App\Services\StagiaireService;
use App\Services\MediaService;
use App\Services\AgendaService;
use App\Services\DashboardService;

class ProfileController extends Controller
{
    protected $formationService;
    protected $stagiaireService;
    protected $mediaService;
    protected $agendaService;
    protected $dashboardService;

    public function __construct(
        FormationService $formationService,
        StagiaireService $stagiaireService,
        MediaService $mediaService,
        AgendaService $agendaService,
        DashboardService $dashboardService
    ) {
        $this->formationService = $formationService;
        $this->stagiaireService = $stagiaireService;
        $this->mediaService = $mediaService;
        $this->agendaService = $agendaService;
        $this->dashboardService = $dashboardService;
    }

    public function show($id)
    {
        $stagiaire = $this->stagiaireService->show($id);
        if (!$stagiaire) {
            return response()->json(['message' => 'Stagiaire not found'], 404);
        }

        $stats = $this->dashboardService->getStagiaireDashboard($id);
        $formations = $this->formationService->getFormationsByStagiaire($id);
        $agenda = $this->agendaService->getStagiaireAgenda($id);
        $notifications = $this->agendaService->getStagiaireNotifications($id);
        $media = $this->mediaService->getTutorials();

        return response()->json([
            'stagiaire' => $stagiaire,
            'stats' => $stats,
            'formations' => $formations,
            'agenda' => $agenda,
            'notifications' => $notifications,
            'media' => $media
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:stagiaires,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'bio' => 'sometimes|string|max:1000',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $updated = $this->stagiaireService->update($id, $validated);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update stagiaire'], 500);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'stagiaire' => $this->stagiaireService->show($id)
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $updated = $this->stagiaireService->updatePassword($id, $validated);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update password'], 500);
        }

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function exportAgenda($id)
    {
        $icsContent = $this->agendaService->exportAgendaToICS($id);

        return response($icsContent)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="agenda.ics"');
    }

    public function markNotificationAsRead($id, $notificationId)
    {
        $marked = $this->agendaService->markNotificationAsRead($notificationId);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark notification as read'], 500);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllNotificationsAsRead($id)
    {
        $marked = $this->agendaService->markAllNotificationsAsRead($id);

        if (!$marked) {
            return response()->json(['message' => 'Failed to mark notifications as read'], 500);
        }

        return response()->json(['message' => 'All notifications marked as read']);
    }
}

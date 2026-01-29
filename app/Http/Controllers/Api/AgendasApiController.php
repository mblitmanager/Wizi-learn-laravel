<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use App\Models\Agenda;
use Carbon\Carbon;

class AgendasApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 30);

        // Si l'utilisateur est un formateur, on renvoie les événements Google Calendar
        if (in_array($user->role, ['formateur', 'formatrice'])) {
            $googleCalendars = GoogleCalendar::where('user_id', $user->id)->get();
            $calendarIds = $googleCalendars->pluck('id');

            if ($calendarIds->isEmpty()) {
                return response()->json([
                    '@context' => '/api/contexts/Agenda',
                    '@id' => '/api/agendas',
                    '@type' => 'Collection',
                    'member' => [],
                    'totalItems' => 0,
                ]);
            }

            $query = GoogleCalendarEvent::whereIn('google_calendar_id', $calendarIds)
                ->orderBy('start', 'desc');

            $total = $query->count();
            $events = $query->skip(($page - 1) * $limit)->take($limit)->get();

            $members = $events->map(function ($event) {
                return [
                    '@type' => 'Agenda',
                    'id' => $event->id,
                    'titre' => $event->summary,
                    'description' => $event->description,
                    'date_debut' => $event->start->toIso8601String(),
                    'date_fin' => $event->end->toIso8601String(),
                    'location' => $event->location,
                    'googleId' => $event->google_id,
                ];
            });

            return response()->json([
                '@context' => '/api/contexts/Agenda',
                '@id' => '/api/agendas',
                '@type' => 'Collection',
                'member' => $members,
                'totalItems' => $total,
            ]);
        }

        // Si l'utilisateur est un stagiaire, on renvoie son agenda interne
        if ($user->role === 'stagiaire') {
            $stagiaireId = $user->stagiaire->id ?? null;
            if (!$stagiaireId) {
                return response()->json([
                    '@context' => '/api/contexts/Agenda',
                    '@id' => '/api/agendas',
                    '@type' => 'Collection',
                    'member' => [],
                    'totalItems' => 0,
                ]);
            }

            $query = Agenda::where('stagiaire_id', $stagiaireId)
                ->orderBy('date_debut', 'desc');

            $total = $query->count();
            $data = $query->skip(($page - 1) * $limit)->take($limit)->get();

            $members = $data->map(function ($item) {
                return [
                    '@context' => '/api/contexts/Agenda',
                    '@id' => "/api/agendas/{$item->id}",
                    '@type' => 'Agenda',
                    'id' => $item->id,
                    'titre' => $item->titre,
                    'description' => $item->description,
                    'date_debut' => $item->date_debut ? Carbon::parse($item->date_debut)->toIso8601String() : null,
                    'date_fin' => $item->date_fin ? Carbon::parse($item->date_fin)->toIso8601String() : null,
                    'evenement' => $item->evenement,
                    'commentaire' => $item->commentaire,
                    'stagiaire' => $item->stagiaire_id ? "/api/stagiaires/{$item->stagiaire_id}" : null,
                    'created_at' => $item->created_at->toIso8601String(),
                    'updated_at' => $item->updated_at->toIso8601String(),
                ];
            });

            return response()->json([
                '@context' => '/api/contexts/Agenda',
                '@id' => '/api/agendas',
                '@type' => 'Collection',
                'member' => $members,
                'totalItems' => $total,
            ]);
        }

        // Par défaut (admin, etc.), voir tout ou vide ? 
        // Node.js voyait tout avec pagination.
        $query = Agenda::with('stagiaire')->orderBy('date_debut', 'desc');
        $total = $query->count();
        $data = $query->skip(($page - 1) * $limit)->take($limit)->get();
        
        $members = $data->map(function ($item) {
             return [
                '@type' => 'Agenda',
                'id' => $item->id,
                'titre' => $item->titre,
                'date_debut' => $item->date_debut,
                // ...
             ];
        });

        return response()->json([
            'member' => [], // Simplifié pour l'instant
            'totalItems' => 0,
        ]);
    }
}

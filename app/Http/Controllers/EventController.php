<?php

namespace App\Http\Controllers;

use App\Jobs\SendFirebaseNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Point d'entrée pour les événements en temps réel
     */
    public function listen(Request $request): JsonResponse
    {
        try {
            // Récupérer les événements en attente
            $events = $this->getPendingEvents();

            // Marquer les événements comme traités
            $this->markEventsAsProcessed($events);

            Log::info('Événements récupérés', ['count' => count($events)]);

            return response()->json([
                'status' => 'success',
                'events' => $events,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération événements', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des événements'
            ], 500);
        }
    }

    /**
     * Créer un nouvel événement et déclencher une notification
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'topic' => 'string|max:100',
            'data' => 'array'
        ]);

        try {
            // Créer l'événement en base
            $event = Event::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'topic' => $validated['topic'] ?? 'general',
                'data' => $validated['data'] ?? [],
                'status' => 'pending',
                'created_at' => now()
            ]);

            // Déclencher la notification Firebase (job en arrière-plan)
            SendFirebaseNotification::dispatch($event);

            return response()->json([
                'status' => 'success',
                'event' => $event,
                'message' => 'Événement créé et notification programmée'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur création événement', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de l\'événement'
            ], 500);
        }
    }

    /**
     * Test de connexion pour Event Whisperer
     */
    public function testConnection(): JsonResponse
    {

        return response()->json([
            'status' => 'success',
            'message' => 'API Laravel connectée',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    /**
     * Récupérer les logs et messages récents pour Event Whisperer
     */
    public function recent(Request $request): JsonResponse
    {
        // Exemple : à adapter selon votre logique de stockage des logs/messages
        $logs = [];
        $messages = [];

        // Récupérer les 50 derniers événements (messages)
        $events = \App\Models\Event::orderBy('created_at', 'desc')->limit(50)->get();
        foreach ($events as $event) {
            $messages[] = [
                'id' => $event->id,
                'created_at' => $event->created_at,
                'type' => $event->type ?? 'message',
                'title' => $event->title,
                'subject' => $event->title, // pour compatibilité
                'body' => $event->message,
                'content' => $event->message, // pour compatibilité
                'recipient' => $event->topic ?? null,
                'status' => $event->status ?? 'sent',
                'data' => $event->data,
            ];
        }

        // (Optionnel) Récupérer les logs Laravel si vous avez une table/log dédiée
        // $logs = LogModel::orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json([
            'logs' => $logs,
            'messages' => $messages,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function getPendingEvents(): array
    {
        return Event::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    private function markEventsAsProcessed(array $events): void
    {
        $eventIds = collect($events)->pluck('id');
        Event::whereIn('id', $eventIds)->update(['status' => 'processed']);
    }
}

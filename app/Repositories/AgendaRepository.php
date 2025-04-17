<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Interfaces\AgendaRepositoryInterface;
use Carbon\Carbon;

class AgendaRepository implements AgendaRepositoryInterface
{
    public function getEventsByStagiaire($stagiaireId)
    {
        return Event::where('stagiaire_id', $stagiaireId)
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getUpcomingEvents($stagiaireId)
    {
        return Event::where('stagiaire_id', $stagiaireId)
            ->where('start_date', '>=', Carbon::now())
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function createEvent(array $data): Event
    {
        return Event::create($data);
    }

    public function updateEvent($id, array $data): bool
    {
        $event = Event::find($id);
        return $event ? $event->update($data) : false;
    }

    public function deleteEvent($id): bool
    {
        $event = Event::find($id);
        return $event ? $event->delete() : false;
    }

    public function getEventById($id): ?Event
    {
        return Event::find($id);
    }
} 
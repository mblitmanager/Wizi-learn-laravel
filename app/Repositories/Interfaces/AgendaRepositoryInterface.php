<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;

interface AgendaRepositoryInterface
{
    public function getEventsByStagiaire($stagiaireId);
    public function getUpcomingEvents($stagiaireId);
    public function createEvent(array $data): Event;
    public function updateEvent($id, array $data): bool;
    public function deleteEvent($id): bool;
    public function getEventById($id): ?Event;
} 
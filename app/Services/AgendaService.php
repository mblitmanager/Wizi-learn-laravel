<?php

namespace App\Services;

use App\Repositories\Interfaces\AgendaRepositoryInterface;
use App\Repositories\Interfaces\FormationRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Carbon\Carbon;

class AgendaService
{
    protected $agendaRepository;
    protected $formationRepository;
    protected $notificationRepository;

    public function __construct(
        AgendaRepositoryInterface $agendaRepository,
        FormationRepositoryInterface $formationRepository,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->agendaRepository = $agendaRepository;
        $this->formationRepository = $formationRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function getStagiaireAgenda($stagiaireId)
    {
        $formations = $this->formationRepository->getFormationsByStagiaire($stagiaireId);
        $events = $this->agendaRepository->getEventsByStagiaire($stagiaireId);

        return [
            'formations' => $formations,
            'events' => $events,
            'upcoming_events' => $events->where('start_date', '>=', Carbon::now())->sortBy('start_date')
        ];
    }

    public function exportAgendaToICS($stagiaireId)
    {
        $events = $this->agendaRepository->getEventsByStagiaire($stagiaireId);
        $icsContent = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//YourApp//NONSGML v1.0//EN\r\n";

        foreach ($events as $event) {
            $icsContent .= "BEGIN:VEVENT\r\n";
            $icsContent .= "DTSTART:" . $event->start_date->format('Ymd\THis\Z') . "\r\n";
            $icsContent .= "DTEND:" . $event->end_date->format('Ymd\THis\Z') . "\r\n";
            $icsContent .= "SUMMARY:" . $event->title . "\r\n";
            $icsContent .= "DESCRIPTION:" . $event->description . "\r\n";
            $icsContent .= "END:VEVENT\r\n";
        }

        $icsContent .= "END:VCALENDAR";
        return $icsContent;
    }

    public function getStagiaireNotifications($stagiaireId)
    {
        return $this->notificationRepository->getNotificationsByStagiaire($stagiaireId);
    }
} 
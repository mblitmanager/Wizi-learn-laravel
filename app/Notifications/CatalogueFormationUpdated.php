<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\CatalogueFormation;

class CatalogueFormationUpdated extends Notification
{
    use Queueable;

    protected $formation;

    public function __construct(CatalogueFormation $formation)
    {
        $this->formation = $formation;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Mise à jour du Catalogue de Formation')
            ->line('Une formation a été mise à jour : ' . $this->formation->title)
            ->action('Voir la Formation', url('/catalogue-formation/' . $this->formation->id))
            ->line('Découvrez les nouveautés !');
    }

    public function toArray($notifiable)
    {
        return [
            'formation_id' => $this->formation->id,
            'title' => $this->formation->title,
            'message' => 'Une formation a été mise à jour : ' . $this->formation->title
        ];
    }
}

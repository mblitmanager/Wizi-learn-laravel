<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Media;

class MediaCreated extends Notification
{
    use Queueable;

    protected $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau Média Disponible')
            ->line('Un nouveau média a été ajouté : ' . $this->media->title)
            ->action('Voir le Média', url('/media/' . $this->media->id))
            ->line('N\'oubliez pas de le consulter !');
    }

    public function toArray($notifiable)
    {
        return [
            'media_id' => $this->media->id,
            'title' => $this->media->title,
            'message' => 'Un nouveau média est disponible : ' . $this->media->title
        ];
    }
}

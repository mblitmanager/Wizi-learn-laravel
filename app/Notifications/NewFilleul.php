<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class NewFilleul extends Notification
{
    use Queueable;

    protected $filleul;

    public function __construct(User $filleul)
    {
        $this->filleul = $filleul;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau Filleul')
            ->line('Vous avez un nouveau filleul : ' . $this->filleul->name)
            ->action('Voir le Profil', url('/profile/' . $this->filleul->id))
            ->line('Accompagnez-le dans son parcours !');
    }

    public function toArray($notifiable)
    {
        return [
            'filleul_id' => $this->filleul->id,
            'name' => $this->filleul->name,
            'message' => 'Vous avez un nouveau filleul : ' . $this->filleul->name
        ];
    }
}

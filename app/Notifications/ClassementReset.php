<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClassementReset extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Réinitialisation du Classement')
            ->line('Le classement a été réinitialisé !')
            ->action('Voir le Classement', url('/classement'))
            ->line('C\'est le moment de reprendre la course !');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Le classement a été réinitialisé. C\'est le moment de reprendre la course !'
        ];
    }
}

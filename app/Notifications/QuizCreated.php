<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Quiz;

class QuizCreated extends Notification
{
    use Queueable;

    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau Quiz Disponible')
            ->line('Un nouveau quiz a été créé : ' . $this->quiz->title)
            ->action('Voir le Quiz', url('/quiz/' . $this->quiz->id))
            ->line('Bonne chance !');
    }

    public function toArray($notifiable)
    {
        return [
            'quiz_id' => $this->quiz->id,
            'title' => $this->quiz->title,
            'message' => 'Un nouveau quiz est disponible : ' . $this->quiz->title
        ];
    }
}

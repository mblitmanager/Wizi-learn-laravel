<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Quiz;

class NewQuizNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function build()
    {
        // Pour le logo principal (petit), utiliser base64
        $logoData = base64_encode(file_get_contents(public_path('assets/logo_wizi.png')));

        // Pour la grande image, utiliser pièce jointe
        return $this->subject('Nouveau quiz disponible - ' . $this->quiz->titre)
            ->view('emails.new_quiz_notification', [
                'quiz' => $this->quiz,
                'logoData' => $logoData
            ])
            ->attach(public_path('assets/online.png'), [
                'as' => 'online.png',
                'mime' => 'image/png',
            ]);
    }
}

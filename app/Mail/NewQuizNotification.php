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
        return $this->subject('Nouveau quiz disponible - ' . $this->quiz->titre)
            ->view('emails.new_quiz_notification')
            ->with([
                'quiz' => $this->quiz,
            ]);
    }
}

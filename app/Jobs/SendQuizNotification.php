<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Quiz;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewQuizNotification;

class SendQuizNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $quiz;
    public $email;

    /**
     * Create a new job instance.
     */
    public function __construct(Quiz $quiz, string $email)
    {
        $this->quiz = $quiz;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new NewQuizNotification($this->quiz));
    }
}

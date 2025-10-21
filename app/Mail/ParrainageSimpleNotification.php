<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParrainageSimpleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $civilite;
    public $prenom;

    public function __construct($civilite, $prenom)
    {
        $this->civilite = $civilite;
        $this->prenom = $prenom;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre demande de parrainage')
            ->view('emails.parrainage_simple')
            ->with([
                'civilite' => $this->civilite,
                'prenom' => $this->prenom,
            ]);
    }
}

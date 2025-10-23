<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParrainageSimpleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $nomComplet;

    public function __construct($nomComplet)
    {
        $this->nomComplet = $nomComplet;
    }

    public function build()
    {
        return $this->subject('Nouvelle inscription parrainage - ' . $this->nomComplet)
            ->view('emails.parrainage_simple')
            ->with([
                'nomComplet' => $this->nomComplet,
            ]);
    }
}

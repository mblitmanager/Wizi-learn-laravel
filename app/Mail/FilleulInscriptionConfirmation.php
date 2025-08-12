<?php

namespace App\Mail;

use App\Models\CatalogueFormation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FilleulInscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $filleul;
    public $parrain;
    public $formation;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $filleul, User $parrain, CatalogueFormation $formation)
    {
        $this->filleul = $filleul;
        $this->parrain = $parrain;
        $this->formation = $formation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Confirmation de votre inscription')
            ->view('emails.filleul_inscription_confirmation', [
                'logo' => public_path('assets/logo_wizi.png'),
            ]);
    }
}

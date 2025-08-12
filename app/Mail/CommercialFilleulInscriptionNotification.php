<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Commercial;
use App\Models\CatalogueFormation;

class CommercialFilleulInscriptionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $filleul;
    public $parrain;
    public $formation;
    public $commercial;

    public function __construct(User $filleul, User $parrain, CatalogueFormation $formation, Commercial $commercial)
    {
        $this->filleul = $filleul;
        $this->parrain = $parrain;
        $this->formation = $formation;
        $this->commercial = $commercial;
    }

    public function build()
    {
        return $this->subject('Nouveau filleul inscrit pour votre parrain')
            ->view('emails.commercial_inscription')
            ->with([
                'filleul' => $this->filleul,
                'parrain' => $this->parrain,
                'formation' => $this->formation,
                'commercial' => $this->commercial,
            ]);
    }
}

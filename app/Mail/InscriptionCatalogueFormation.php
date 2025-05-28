<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionCatalogueFormation extends Mailable
{
    use Queueable, SerializesModels;

    public $stagiaire;
    public $catalogueFormation;
    public $isPoleRelation;

    public function __construct($stagiaire, $catalogueFormation, $isPoleRelation = false)
    {
        $this->stagiaire = $stagiaire;
        $this->catalogueFormation = $catalogueFormation;
        $this->isPoleRelation = $isPoleRelation;
    }

    public function build()
    {
        return $this->subject('Nouvelle inscription à une formation')
            ->view('emails.inscription_catalogue_formation');
    }
}

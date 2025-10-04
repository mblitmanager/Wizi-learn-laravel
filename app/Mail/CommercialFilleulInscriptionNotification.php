<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Commercial;

class CommercialFilleulInscriptionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $filleul;
    public $parrain;
    public $commercial;
    public $formData;

    public function __construct(User $filleul, User $parrain, Commercial $commercial, array $formData = [])
    {
        $this->filleul = $filleul;
        $this->parrain = $parrain;
        $this->commercial = $commercial;
        $this->formData = $formData;
    }

    public function build()
    {
        return $this->subject('Nouvelle inscription - Filleul à contacter')
            ->view('emails.commercial_inscription')
            ->with([
                'filleul' => $this->filleul,
                'parrain' => $this->parrain,
                'commercial' => $this->commercial,
                'formData' => $this->formData,
            ]);
    }
}

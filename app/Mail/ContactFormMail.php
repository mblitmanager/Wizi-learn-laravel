<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;

    public function __construct(array $data)
    {
        $this->mailData = $data;
    }

    public function build()
    {
        return $this->from($this->mailData['email'])
            ->subject($this->mailData['subject'])
            ->view('emails.contact-form')
            ->with(['data' => $this->mailData]);
    }
}

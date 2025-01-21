<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChauffeurWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chauffeur;

    /**
     * Create a new message instance.
     *
     * @param $chauffeur
     */
    public function __construct($chauffeur)
    {
        $this->chauffeur = $chauffeur;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenue chez nous !')
                    ->view('emails.chauffeur_welcome'); // Assurez-vous de créer cette vue
    }
}
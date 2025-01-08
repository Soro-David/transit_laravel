<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Importez le modèle User

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // Propriété pour stocker l'utilisateur

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Bienvenue sur notre plateforme !')
                    ->view('emails.user_registered'); // Vue pour l'email
    }
}
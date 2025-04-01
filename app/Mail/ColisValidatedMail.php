<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ColisValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $colis;

    /**
     * Create a new message instance.
     */
    public function __construct($colis)
    {
        $this->colis = $colis;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre Colis a été Validé - Référence: ' . $this->colis->reference_colis,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.colis_validated',
            with: [
                'expediteur_nom' => $this->colis->expediteur->nom . ' ' . $this->colis->expediteur->prenom,
                'reference_colis' => $this->colis->reference_colis,
                'agence_expedition' => $this->colis->destinataire->agence ?? 'N/A', // Assuming destinataire has agence for sender's agency info on form
                'agence_destination' => $this->colis->destinataire->agence ?? 'N/A',
                'mode_transit' => $this->colis->mode_transit ?? 'N/A',
                'description_colis' => $this->colis->description_colis ?? 'N/A',
                'prix_transit_colis' => $this->colis->prix_transit_colis ?? 'N/A',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
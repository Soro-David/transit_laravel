<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $colis;
    public $paiementData;

    /**
     * Create a new message instance.
     */
    public function __construct($colis, $paiementData)
    {
        $this->colis = $colis;
        $this->paiementData = $paiementData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Paiement Confirmé - Colis Référence: ' . $this->colis->reference_colis,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_confirmed',
            with: [
                'expediteur_nom' => $this->colis->expediteur->nom . ' ' . $this->colis->expediteur->prenom,
                'reference_colis' => $this->colis->reference_colis,
                'methode_paiement' => $this->paiementData['methode_paiement'] ?? 'N/A',
                'montant_paye' => $this->paiementData['montant'] ?? 'N/A',
                'date_validation' => now()->format('d/m/Y H:i:s'), // Formater la date si nécessaire
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
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\Paiement; // Assurez-vous que le chemin vers votre modèle Paiement est correct

class ColisValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Paiement $paiement;
    public Collection $colisCollection;

    /**
     * Create a new message instance.
     */
    public function __construct(Paiement $paiement, Collection $colisCollection)
    {
        $this->paiement = $paiement;
        $this->colisCollection = $colisCollection;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $reference = $this->colisCollection->first()->reference_colis ?? 'N/A';
        return new Envelope(
            subject: 'Confirmation de votre dossier - Référence: ' . $reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $firstColis = $this->colisCollection->first();
        $expediteur = $this->paiement->expediteur;
        $destinataire = $firstColis->destinataire;

        return new Content(
            view: 'emails.colis_validated',
            with: [
                'expediteur_nom' => $expediteur->nom . ' ' . $expediteur->prenom,
                'reference_colis' => $firstColis->reference_colis,
                'agence_expedition' => $expediteur->agence,
                'agence_destination' => $destinataire->agence,
                'mode_transit' => $firstColis->mode_transit,
                'paiement' => $this->paiement,
                'colisDetails' => $this->colisCollection,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
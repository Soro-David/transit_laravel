<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Devis;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Clients;
use Barryvdh\DomPDF\Facade\Pdf as PDF; // ajouter en haut du fichier avec les autres use
use Illuminate\Support\Facades\Auth;

class ClientFactureController extends Controller
{
    /**
     * Affiche la liste des devis confirmés pour l'utilisateur connecté.
     */
    // public function index()
    // {
    //     $userId = Auth::id();

    //     // Récupère les devis de l'utilisateur avec leurs items, uniquement ceux "confirmé"
    //     $devisList = Devis::with('devisItems')
    //         ->where('user_id', $userId)
    //         ->where('etat', 'confirmé')
    //         ->orderByDesc('created_at')
    //         ->get();

    //     return view('customer.invoice.facture', compact('devisList'));
    // }

    // /**
    //  * Affiche un devis (détail) par référence (optionnel).
    //  */
    // public function show($reference)
    // {
    //     $userId = Auth::id();

    //     $devis = Devis::with('devisItems')
    //         ->where('reference', $reference)
    //         ->where('user_id', $userId)
    //         ->where('etat', 'confirmé')
    //         ->firstOrFail();

    //     return view('customer.invoice.show', compact('devis'));
    // }

    // /**
    //  * (Optionnel) Générer un PDF. Nécessite barryvdh/laravel-dompdf ou autre.
    //  */
    // public function downloadPdf($reference)
    // {
    //     $devis = Devis::with('devisItems')->where('reference', $reference)->firstOrFail();
    
    //     // s'assurer que la vue existe
    //     if (! view()->exists('customer.invoice.pdf')) {
    //         abort(404, 'Vue PDF introuvable');
    //     }
    
    //     $pdf = PDF::loadView('customer.invoice.pdf', compact('devis'))
    //               ->setPaper('a4', 'portrait'); // optionnel
    
    //     return $pdf->download("facture_{$devis->reference}.pdf");
    // }
    public function index()
    {
        $user = Auth::user();

        // Récupère les colis validés où l'expéditeur a le même email et téléphone que l'utilisateur connecté
        $colisList = Colis::where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->orderByDesc('created_at')
            ->get();

        // Transformer les colis en format compatible avec la vue (similaire aux devis)
        $devisList = $colisList->map(function ($colis) {
            return $this->transformColisToDevis($colis);
        });

        return view('customer.invoice.facture', compact('devisList'));
    }

    /**
     * Affiche un colis (détail) par référence.
     */
    public function show($reference)
    {
        $user = Auth::user();

        $colis = Colis::where('reference_colis', $reference)
            ->where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->firstOrFail();

        $devis = $this->transformColisToDevis($colis);

        return view('customer.invoice.show', compact('devis'));
    }

    /**
     * Générer un PDF pour un colis.
     */
    public function downloadPdf($reference)
    {
        $user = Auth::user();

        $colis = Colis::where('reference_colis', $reference)
            ->where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->firstOrFail();

        $devis = $this->transformColisToDevis($colis);

        $pdf = PDF::loadView('customer.invoice.pdf', compact('devis'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("facture_{$colis->reference_colis}.pdf");
    }

    /**
     * Transforme un colis en format compatible avec la vue facture
     */
    private function transformColisToDevis($colis)
    {
        // Créer un objet similaire à Devis pour la compatibilité avec les vues existantes
        $devis = new \stdClass();
        
        // Informations de base
        $devis->id = $colis->id;
        $devis->reference = $colis->reference_colis;
        $devis->created_at = $colis->created_at;
        $devis->updated_at = $colis->updated_at;
        $devis->etat = $colis->etat;
        
        // Informations financières
        $devis->montant = $colis->prix_transit_colis ?? $colis->montant_service ?? 0;
        $devis->devise = $colis->devise ?? 'XOF';
        
        // Informations de transport
        $devis->mode_transit = $colis->mode_transit;
        $devis->mode_de_retrait = $colis->recup;
        
        // Informations expéditeur (basé sur la table expediteurs)
        if ($colis->expediteur) {
            $devis->nom_expediteur = $colis->expediteur->nom ?? 'Expéditeur';
            $devis->prenom_expediteur = $colis->expediteur->prenom ?? '';
            $devis->tel_expediteur = $colis->expediteur->tel ?? 'Non spécifié';
            $devis->email_expediteur = $colis->expediteur->email ?? 'Non spécifié';
            $devis->adresse_expediteur = $colis->expediteur->adresse ?? 'Non spécifié';
        } else {
            // Fallback si pas de relation expediteur
            $devis->nom_expediteur = 'Expéditeur';
            $devis->prenom_expediteur = '';
            $devis->tel_expediteur = 'Non spécifié';
            $devis->email_expediteur = 'Non spécifié';
            $devis->adresse_expediteur = 'Non spécifié';
        }
        
        // Simuler la relation devisItems avec les données du colis
        $devisItem = new \stdClass();
        $devisItem->service = $colis->service;
        $devisItem->description_colis = $colis->description_colis;
        $devisItem->quantite_colis = $colis->quantite_colis ?? 1;
        $devisItem->poids = $colis->poids_colis;
        $devisItem->longueur = $colis->dimension_colis ? explode('x', $colis->dimension_colis)[0] ?? null : null;
        $devisItem->largeur = $colis->dimension_colis ? explode('x', $colis->dimension_colis)[1] ?? null : null;
        $devisItem->hauteur = $colis->dimension_colis ? explode('x', $colis->dimension_colis)[2] ?? null : null;
        $devisItem->valeur_colis = $colis->valeur_colis;
        $devisItem->montant = $colis->prix_transit_colis ?? $colis->montant_service ?? 0;
        $devisItem->type_colis = $colis->type_colis;
        
        $devis->devisItems = collect([$devisItem]);
        
        return $devis;
    }
}
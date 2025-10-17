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

        // Récupère les colis validés groupés par référence
        $colisGrouped = Colis::where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->get()
            ->groupBy('reference_colis');

        // Transformer les groupes de colis en factures
        $devisList = $colisGrouped->map(function ($colisGroup) {
            return $this->transformColisGroupToDevis($colisGroup);
        })->values();

        return view('customer.invoice.facture', compact('devisList'));
    }
    /**
     * Affiche un colis (détail) par référence.
     */
    public function show($reference)
    {
        $user = Auth::user();

        $colisGroup = Colis::where('reference_colis', $reference)
            ->where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->get();

        if ($colisGroup->isEmpty()) {
            abort(404);
        }

        $devis = $this->transformColisGroupToDevis($colisGroup);

        return view('customer.invoice.show', compact('devis'));
    }

    /**
     * Générer un PDF pour un groupe de colis.
     */
    public function downloadPdf($reference)
    {
        $user = Auth::user();

        $colisGroup = Colis::where('reference_colis', $reference)
            ->where('etat', 'Validé')
            ->whereHas('expediteur', function($query) use ($user) {
                $query->where('email', $user->email)
                      ->where('tel', $user->tel);
            })
            ->with(['client', 'expediteur', 'destinataire'])
            ->get();

        if ($colisGroup->isEmpty()) {
            abort(404);
        }

        $devis = $this->transformColisGroupToDevis($colisGroup);

        $pdf = PDF::loadView('customer.invoice.pdf', compact('devis'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("facture_{$reference}.pdf");
    }


    /**
     * Transforme un colis en format compatible avec la vue facture
     */
    private function transformColisGroupToDevis($colisGroup)
    {
        // Prendre le premier colis comme référence pour les informations communes
        $firstColis = $colisGroup->first();
        
        // Créer un objet similaire à Devis pour la compatibilité avec les vues existantes
        $devis = new \stdClass();
        
        // Informations de base
        $devis->id = $firstColis->id;
        $devis->reference = $firstColis->reference_colis;
        $devis->created_at = $firstColis->created_at;
        $devis->updated_at = $firstColis->updated_at;
        $devis->etat = $firstColis->etat;
        
        // Calculer le montant total de tous les colis
        $totalMontant = $colisGroup->sum(function($colis) {
            return $colis->prix_transit_colis ?? $colis->montant_service ?? 0;
        });
        
        $devis->montant = $totalMontant;
        $devis->devise = $firstColis->devise ?? 'XOF';
        
        // Informations de transport
        $devis->mode_transit = $firstColis->mode_transit;
        $devis->mode_de_retrait = $firstColis->recup;
        
        // Informations expéditeur (basé sur la table expediteurs)
        if ($firstColis->expediteur) {
            $devis->nom_expediteur = $firstColis->expediteur->nom ?? 'Expéditeur';
            $devis->prenom_expediteur = $firstColis->expediteur->prenom ?? '';
            $devis->tel_expediteur = $firstColis->expediteur->tel ?? 'Non spécifié';
            $devis->email_expediteur = $firstColis->expediteur->email ?? 'Non spécifié';
            $devis->adresse_expediteur = $firstColis->expediteur->adresse ?? 'Non spécifié';
            $devis->lieu_expedition = $firstColis->expediteur->lieu_expedition ?? 'Non spécifié';
        } else {
            $devis->nom_expediteur = 'Expéditeur';
            $devis->prenom_expediteur = '';
            $devis->tel_expediteur = 'Non spécifié';
            $devis->email_expediteur = 'Non spécifié';
            $devis->adresse_expediteur = 'Non spécifié';
            $devis->lieu_expedition = 'Non spécifié';
        }
        
        // Informations destinataire (si disponible)
        if ($firstColis->destinataire) {
            $devis->nom_destinataire = $firstColis->destinataire->nom ?? '';
            $devis->prenom_destinataire = $firstColis->destinataire->prenom ?? '';
            $devis->tel_destinataire = $firstColis->destinataire->tel ?? 'Non spécifié';
            $devis->adresse_destinataire = $firstColis->destinataire->adresse ?? 'Non spécifié';
            $devis->lieu_destination = $firstColis->destinataire->lieu_destination ?? 'Non spécifié';
        } else {
            $devis->nom_destinataire = '';
            $devis->prenom_destinataire = '';
            $devis->tel_destinataire = 'Non spécifié';
            $devis->adresse_destinataire = 'Non spécifié';
            $devis->lieu_destination = 'Non spécifié';
        }
        
        // Créer un devisItem pour chaque colis dans le groupe
        $devisItems = $colisGroup->map(function ($colis) {
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
            return $devisItem;
        });
        
        $devis->devisItems = $devisItems;
        $devis->colis_count = $colisGroup->count(); // Ajouter le nombre de colis
        
        return $devis;
    }

}
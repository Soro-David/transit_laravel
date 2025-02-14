<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Invoice;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ChineInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_invoice()
    {
        return view('AGENCE_CHINE.invoice.nouvelle');
    }


    // public static function generateInvoiceNumber()
    // {
    //     // Récupérer le dernier numéro de facture utilisé
    //     $lastInvoice = self::orderBy('id', 'desc')->first();
    //     $lastNumber = $lastInvoice ? intval(substr($lastInvoice->numero_facture, 3)) : 0;

    //     $newNumber = $lastNumber + 1;

    //     return '00' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    // }

    public function store_invoice()
    {
        $reference_colis = request()->input('reference_colis');

            // Récupération de la collection de colis avec les jointures nécessaires
        $colisCollection = Colis::where('reference_colis', $reference_colis)
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
                ->whereIn('colis.etat', ['Validé', 'Dechargé'])  // Filtre sur l'état des colis
                ->where('expediteurs.agence', 'Agence de Chine')  // Filtre sur l'agence de l'expéditeur
                ->get();  // Récupérer les résultats de la requête

            // Vérifier si la collection est vide et renvoyer un message d'erreur
            if ($colisCollection->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun colis trouvé avec cette référence.');
            }


        // Récupération du premier colis
        $firstColis = $colisCollection->first();
        
        $date_facture = now();
        $expediteur = $firstColis->expediteur->nom . ' ' . $firstColis->expediteur->prenom;
        $tel_expediteur = $firstColis->expediteur->tel;
        $destinataire = $firstColis->destinataire->nom . ' ' . $firstColis->destinataire->prenom;
        $numero_facture = '00' . str_pad($firstColis->id, 3, '0', STR_PAD_LEFT);


        // Calcul du prix total
        $prix_total = 0;
        foreach ($colisCollection as $colis) {
            if (!isset($colis->prix_transit_colis)) {
                throw new \Exception("Le champ prix_transit_colis est manquant pour un colis.");
            }
            $prix_total += $colis->prix_transit_colis;
        }

        // Utilisation de optional() pour éviter les erreurs si la relation paiement est nulle
        $mode_payement = optional($firstColis->paiement)->mode_de_paiement ?? 'N/A';
        $montant_paye = optional($firstColis->paiement)->montant_reçu ?? 0;
        $reste = $prix_total - $montant_paye;

        $id_agent = Auth::user()->id;
        
        // Création de la facture
        $u=Invoice::create([
            'expediteur_id' => $firstColis->expediteur->id,
            'destinataire_id' => $firstColis->destinataire->id,
            'agent_id' => $id_agent,
            'montant' => $prix_total ?? 0,
            'numero_facture' => $numero_facture,
        ]);
        // dd($u);
        // Préparation des données des colis
        $colisData = [];
        foreach ($colisCollection as $colis) {
            $colisData[] = [
                'description'         => $colis->description_colis,
                'quantite'            => $colis->quantite_colis,
                'poids'               => $colis->poids_colis,
                'type_colis'          => $colis->type_colis,
                'prix_transit_colis'  => $colis->prix_transit_colis,
            ];
        }

        // Passage des données à la vue
        return view('AGENCE_CHINE.invoice.edit_invoice', compact(
            'date_facture', 
            'reference_colis', 
            'expediteur', 
            'tel_expediteur', 
            'destinataire', 
            'prix_total', 
            'montant_paye', 
            'reste', 
            'mode_payement', 
            'colisData',
            'numero_facture',
        ));
    }


    

    public function historique_invoice()
    {
        return view('AGENCE_CHINE.invoice.historique');
    }


    public function edit_invoice()
    {
        return view('AGENCE_CHINE.invoice.edit_invoice');
    }

    public function show_invoice()
    {
        return view('AGENCE_CHINE.invoice.show_invoice');
    }


}

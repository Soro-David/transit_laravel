<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use SimpleSoftwareIO\QrCode\Facades\QrCode; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Les_colis;
use App\Models\Colis;
// use App\Models\Bateaux;
use App\Models\Bateaux;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Article;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AftlbColisController extends Controller
{


    public function store_bateaux(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'reference_bateau' => 'required|unique:bateaux,reference_bateau',
                'reference_conteneur' => 'required',
                'type' => 'required',
                'date_arrive' => 'required|date',
                'compagnie' => 'required|string',
                'agence_destination' => 'required|string',
                // 'agence_expedition' => 'required|string',
            ],
            [
                'reference_bateau.required' => 'La référence du bateau est obligatoire.',
                'reference_bateau.unique' => 'La référence du bateau doit être unique.',
                'reference_conteneur.required' => 'La référence du conteneur est obligatoire.',
                'type.required' => 'Le type de véhicule est obligatoire.',
                'date_arrive.required' => 'La date d\'arrivée est obligatoire.',
                'date_arrive.date' => 'La date d\'arrivée doit être une date valide.',
                'compagnie.required' => 'Le nom de la compagnie est obligatoire.',
                'compagnie.string' => 'Le nom de la compagnie doit être une chaîne de caractères.',
                'agence_destination.required' => 'L\'agence de destination est obligatoire.',
                'agence_destination.string' => 'L\'agence de destination doit être une chaîne de caractères.',
            ]);

            // dd($request);
            // Création du bateau
            $bateau = Bateaux::create([
                'reference_bateau' => $request->reference_bateau,
                'reference_conteneur' => $request->reference_conteneur,
                'type' => $request->type,
                'date_arriver' => $request->date_arrive,
                'compagnie' => $request->compagnie,
                'agence_destination' => $request->agence_destination,
                'agence_expedition' => $request->agence_expedition,
                'nom_bateau' => $request->nom_bateau ?? null,
                'numero_bateau' => $request->numero_bateau ?? null,
                'nom_ballon' => $request->nom_ballon ?? null,
                'numero_ballon' => $request->numero_ballon ?? null,
            ]);

            return redirect()->back()->with('success', 'Bateau créé avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite : ' . $e->getMessage())->withInput();
        }
    }


    public function autocompleteProduit(Request $request)
    {
        $query = $request->get('query');
        $produits = Produit::where('description', 'like', '%' . $query . '%')
                        ->limit(15)
                        ->get(['id', 'description', 'prix']); // Sélectionner les champs à renvoyer
        return response()->json($produits);
    }

    public function storeProduit(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'categorie' => 'required|string|max:100|in:Colis,Service,Remise',
            'prix' => 'required|numeric|min:0',
        ]);
    
        Produit::create([
            'description' => $request->description,
            'categorie' => $request->categorie,
            'prix' => $request->prix,
        ]);
    
        return response()->json(['message' => 'Produit ajouté avec succès !'], 201);
    }


    public function create(Request $request)
    {
        // Récupération des agences et des clients
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        // Redirection vers la vue
        return view('AFT_LOUIS_BLERIOT.colis.add', compact('agences', 'client_expediteurs', 'client_destinataires'));
    }


         /**
     * Génère une référence de colis unique
     *
     * @return string
     */
    private function generateReferenceColis()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        // dd($user);
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw new \Exception("Utilisateur non connecté.");
        }
    
        // Récupérer la première lettre du nom et du prénom
        $firstLetterNom = strtoupper(substr($user->last_name, 0, 1)); // Première lettre du nom
        $firstLetterPrenom = strtoupper(substr($user->first_name, 0, 1)); // Première lettre du prénom
    // dd($firstLetterNom, $firstLetterPrenom);
        // Récupérer la première lettre du mois actuel
        $monthLetter = strtoupper(now()->format('F')[0]); // Première lettre du mois
    
        // Initialiser le chiffre à 1
        $increment = 1;
    
        // Construire la référence de base
        $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";
    
        // Vérifier si la référence existe déjà dans la table colis
        while (DB::table('colis')->where('reference_colis', $baseReference)->exists()) {
            // Incrémenter le chiffre
            $increment++;
            // Mettre à jour la référence avec le nouvel incrément
            $baseReference = "{$firstLetterNom}{$firstLetterPrenom}-{$monthLetter}-{$increment}";
        }
    
        return $baseReference; // Retourner la référence finale
    }
    /**
     * Génère une référence de Contenaire unique
     *
     * @return string
     */

     private function generateReferenceContenaire()
    {
        $alphabet = range('A', 'Z'); // Générer les lettres de A à Z
        $letterIndex = 0; // Commencer par 'A'
        $increment = 1; // Commencer par 1

        do {
            $currentLetter = $alphabet[$letterIndex]; // Obtenir la lettre actuelle
            $baseReference = "{$currentLetter}{$increment}";

            // Vérifier si la référence existe dans la table `colis`
            $exists = DB::table('colis')->where('reference_contenaire', $baseReference)->exists();

            if ($exists) {
                $increment++; // Incrémenter le numéro

                // Si on atteint 6 (au-delà de 5), on passe à la lettre suivante
                if ($increment > 5) {
                    $increment = 1; // Réinitialiser le numéro
                    $letterIndex++; // Passer à la lettre suivante
                }
            }
        } while ($exists && $letterIndex < count($alphabet)); // Continuer tant qu'on trouve une référence existante

        return $baseReference; // Retourner la référence générée
    }
    
    private function generateReferenceVol()
    {
        $alphabet = range('A', 'Z'); // Générer les lettres de A à Z
        $letterIndex = 0; // Commencer par 'A'
        $increment = 1; // Commencer par 1
    
        do {
            $currentLetter = $alphabet[$letterIndex]; // Obtenir la lettre actuelle
            $baseReference = "{$currentLetter}{$increment}";
    
            // Vérifier si la référence existe dans la table `colis`
            $exists = DB::table('colis')->where('reference_contenaire', $baseReference)->exists();
    
            if ($exists) {
                $increment++; // Incrémenter le numéro
    
                // Si on atteint 6 (au-delà de 5), on passe à la lettre suivante
                if ($increment > 5) {
                    $increment = 1; // Réinitialiser le numéro
                    $letterIndex++; // Passer à la lettre suivante
                }
            }
        } while ($exists && $letterIndex < count($alphabet)); // Continuer tant qu'on trouve une référence existante
    
        return $baseReference;// Retourner la référence finale
    }
    /**
     * Étape de paiement.
     */
    // public function  ement()
    // {
    //     return view('admin.colis.add.payement');
    // }

    /**
     * Étape 1 : Formulaire initial.
     */

     public function add_colis(Request $request)
{
    // Récupérer les pays sans doublons
    $paysUniques = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')
                     ->distinct()
                     ->pluck('pays_agence');


    // Récupérer les agences avec leur pays associé
    $agences = Agence::select('nom_agence', 'pays_agence', 'id')->get();
    // $agencesExpedition = Agence::where('pays_agence', '!=', 'Côte d\'Ivoire')->get();
    $agencesExpedition = Agence::where('nom_agence', 'AFT Agence Louis Bleriot')->get();
    $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();


    $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
    return view('AFT_LOUIS_BLERIOT.colis.add_colis', compact('agencesExpedition','agencesDestination', 'referenceColis', 'paysUniques'));
}


public function store_colis(Request $request)
{
    try {
        // Sauvegarde des données de la première étape dans la session
        $request->session()->put('step1', $request->all());

        session(['step1' => $request->only([
            'nom_expediteur',
            'prenom_expediteur', 
            'email_expediteur', 
            'tel_expediteur',
            'adresse_expediteur',
            'agence_expedition', 
            'nom_destinataire', 
            'prenom_destinataire',
            'email_destinataire', 
            'tel_destinataire',
            'adresse_destinataire',
            'agence_destination',
            'mode_transit',
            'reference_colis',
            'quantite_colis',
            'service',
            'hauteur',
            'largeur',
            'longueur',
            'dimension_result',
            'type_colis',
            'poids',
            'description_colis',
            'prix' // Assurez-vous que 'prix' est bien envoyé depuis le formulaire
        ])]);

        return redirect()->route('aftlb_colis.create.payement');
    } catch (\Exception $e) {
        \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());
        return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
    }
}
    // return redirect()->route('aftlb_colis.generer.qrcode');

    public function stepPayment()
    {
       
        return view('AFT_LOUIS_BLERIOT.colis.add.payement');
    }
    public function storePayment(Request $request)
    {
        try {
            $validatedData = $request->validate([
            //     'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            //     'numero_compte' => 'required_if:mode_payement,bank|max:255',
            //     'nom_banque' => 'required_if:mode_payement,bank,cheque|max:255',
            //     'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
            //     'numero_tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            //     'operateur_mobile' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            //     'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            //     'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
            // ], [
            //     'required' => 'Le champ :attribute est obligatoire.',
            //     'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            //     'numeric' => 'Le champ :attribute doit être un nombre.',
            //     'min' => 'Le champ :attribute doit être au moins :min.',
    
            //     'mode_payement.required' => 'Veuillez sélectionner un mode de paiement.',
            //     'mode_payement.in' => 'Le mode de paiement sélectionné est invalide.',
    
            //     'numero_compte.required_if' => 'Le numéro de compte est requis pour les paiements bancaires.',
            //     'nom_banque.required_if' => 'Le nom de la banque est requis pour ce mode de paiement.',
            //     'transaction_id.required_if' => 'L\'identifiant de transaction est obligatoire pour ce mode de paiement.',
            //     'numero_tel.required_if' => 'Le numéro de téléphone est requis pour les paiements mobile.',
            //     'numero_tel.regex' => 'Le numéro de téléphone doit contenir entre 10 et 15 chiffres.',
            //     'operateur_mobile.required_if' => 'Veuillez sélectionner un opérateur mobile.',
            //     'operateur_mobile.in' => 'L\'opérateur mobile sélectionné est invalide.',
            //     'numero_cheque.required_if' => 'Le numéro de chèque est requis pour les paiements par chèque.',
            //     'montant_reçu.required_if' => 'Le montant reçu est obligatoire pour les paiements en espèces.',
            //     'montant_reçu.min' => 'Le montant reçu doit être supérieur à zéro.',
            ]);
    
            // Stocker les données en session
            
            session(['step2' => $request->only([
                'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
                'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
            ])]);
            return response()->json([
                'success' => true,
                'redirect' => route('aftlb_colis.generer.qrcode'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Retourne les erreurs de validation sous forme de tableau associatif
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }
  
    public function generer_qrcode(Request $request)
    {
        // dd($request);
        // Fusionner toutes les données de session dans un tableau
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );
    
        // Vérifiez que la clé 'quantite_colis' existe
        if (!isset($data['quantite_colis']) || !is_array($data['quantite_colis'])) {
            return redirect()->back()->with('error', 'La quantité des colis est manquante ou invalide.');
        }
    
        // Ajouter le statut au tableau de données
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';
    
        // Vérifiez que les données sont bien réparties pour chaque table
        $expediteurData = [
            'nom' => $data['nom_expediteur'] ?? null,
            'prenom' => $data['prenom_expediteur'] ?? null,
            'email' => $data['email_expediteur'] ?? null,
            'tel' => $data['tel_expediteur'] ?? null,
            'agence' => $data['agence_expedition'] ?? null,
            'adresse' => $data['adresse_expediteur'] ?? null,
        ];
    
        $destinataireData = [
            'nom' => $data['nom_destinataire'] ?? null,
            'prenom' => $data['prenom_destinataire'] ?? null,
            'email' => $data['email_destinataire'] ?? null,
            'tel' => $data['tel_destinataire'] ?? null,
            'agence' => $data['agence_destination'] ?? null,
            'adresse' => $data['adresse_destinataire'] ?? null,
        ];
    
        // Initialisation du tableau pour stocker les données des colis
        $colisData = [];
    
        // Parcourir les tableaux pour construire $colisData
        foreach ($data['quantite_colis'] as $index => $quantite) {
            $hauteur = $data['hauteur'][$index] ?? null;
            $largeur = $data['largeur'][$index] ?? null;
            $longueur = $data['longueur'][$index] ?? null;
    
            if (isset($hauteur, $largeur, $longueur)) {
                $dimension_result = "{$hauteur}x{$largeur}x{$longueur}";
            } else {
                $dimension_result = null;
            }
    
            $colisData[] = [
                'reference_colis' => $data['reference_colis'],
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => $quantite,
                'service' => $data['service'][$index] ?? null,
                'prix_transit_colis' => $data['prix'][$index] ?? null,
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                'dimension_result' => $data['dimension_result'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'] ?? null,
                'etat' => $data['etat'] ?? null,
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result,
                'description_colis' => $data['description_colis'][$index] ?? null,
            ];
        }
    
        // Insérer les données dans chaque table
        $expediteur = Expediteur::create($expediteurData);
        $destinataire = Destinataire::create($destinataireData);
    
        // Récupérer les données de paiement depuis la session `step2`
        $payementDataSession = session('step2', []);
    
    // **Récupérer cinetpay_transaction_id depuis la requête**
    $cinetpayTransactionId = $request->input('cinetpay_transaction_id');
    $manualTransactionId = $payementDataSession['transaction_id'] ?? null; // Fallback for manual transaction ID if CinetPay is not used
       
    // Préparer les données de paiement pour la base de données
        $paiementData = [
            'colis_id' => null, // Sera mis à jour après la création du colis
            'methode_paiement' => $payementDataSession['mode_payement'] ?? null,
            'montant' => session('step1.prix.0') ?? null, // Récupérer le montant depuis la session step1 (ou ajustez selon votre logique)
            'operateur' => $payementDataSession['operateur_mobile'] ?? null, // Pour Mobile Money
            'banque' => $payementDataSession['nom_banque'] ?? null, // Pour Virement Bancaire et Chèque
            'NumeroPaiement' => $payementDataSession['numero_tel'] ?? $payementDataSession['numero_cheque'] ?? $payementDataSession['numero_compte'] ?? null, // Numéro de tel pour mobile money, cheque ou compte bancaire
            'id_transaction' => $payementDataSession['transaction_id'] ?? null,
            'statut_paiement' => 'payé', // Statut par défaut, vous pouvez ajuster la logique si nécessaire
            'date_validation' => now(), // Date de validation du paiement
            'expediteur_id' => $expediteur->id, // ID de l'expéditeur
            'agent_id' => Auth::id(), // ID de l'agent connecté
        ];
    
        // Créer les colis et enregistrer les paiements
        $colis = [];
        foreach ($colisData as $colisItem) {
            // Créer le colis
            $colisModel = Colis::create(array_merge($colisItem, [
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
            ]));
            $colis[] = $colisModel; // Ajouter au tableau pour la génération du QR code
    
            // Créer et enregistrer le paiement pour ce colis
            $paiement = Paiement::create(array_merge($paiementData, ['colis_id' => $colisModel->id])); // Associer le paiement au colis
        }
     // **Association explicite (important)**
     $colisModel->paiement()->associate($paiement);
     $colisModel->save();
        // Générer les QR codes pour chaque colis
        foreach ($colis as $colisItem) {
            // Données à encoder dans le QR code
            $qrData = [
                'Référence colis' => $colisItem->reference_colis,
                'Statut' => $colisItem->etat,
                'Nom Expéditeur' => $expediteur->nom . ' ' . $expediteur->prenom,
                'Nom Destinataire' => $destinataire->nom . ' ' . $destinataire->prenom,
                'Téléphone Destinataire' => $destinataire->tel,
                'Agence Destination' => $destinataire->agence ?? '',
                'Lieu de Destination' => $destinataire->adresse ?? '',
            ];
    
            // Construire le contenu du QR code
            $qrCodeContent = '';
            foreach ($qrData as $key => $value) {
                $qrCodeContent .= "{$key}: {$value}\n";
            }
    
            // Générer le QR code
            $qrCode = new QrCode($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $pngData = $result->getString();
    
            // Définir le chemin du fichier QR code
            $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
            $fullPath = public_path($filePath);
    
            // Vérifier et créer le répertoire cible si nécessaire
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
    
            // Sauvegarder le fichier QR code dans le storage
            file_put_contents($fullPath, $pngData);
    
            // Mettre à jour le chemin du QR code dans la base de données
            $colisItem->update(['qr_code_path' => $filePath]);
        }
    
        // Réinitialiser les sessions après traitement
        session()->forget(['step1', 'step2']);
    
        // Retourner la vue avec les informations nécessaires
        return view('AFT_LOUIS_BLERIOT.colis.add.complete', compact('colis', 'filePath', 'fullPath', 'result'));
    } 

    public function storePayement(Request $request)
    {
        try {
            $validatedData = $request->validate([
            //     'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            //     'numero_compte' => 'required_if:mode_payement,bank|max:255',
            //     'nom_banque' => 'required_if:mode_payement,bank,cheque|max:255',
            //     'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
            //     'numero_tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            //     'operateur_mobile' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            //     'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            //     'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
            // ], [
            //     'required' => 'Le champ :attribute est obligatoire.',
            //     'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            //     'numeric' => 'Le champ :attribute doit être un nombre.',
            //     'min' => 'Le champ :attribute doit être au moins :min.',
    
            //     'mode_payement.required' => 'Veuillez sélectionner un mode de paiement.',
            //     'mode_payement.in' => 'Le mode de paiement sélectionné est invalide.',
    
            //     'numero_compte.required_if' => 'Le numéro de compte est requis pour les paiements bancaires.',
            //     'nom_banque.required_if' => 'Le nom de la banque est requis pour ce mode de paiement.',
            //     'transaction_id.required_if' => 'L\'identifiant de transaction est obligatoire pour ce mode de paiement.',
            //     'numero_tel.required_if' => 'Le numéro de téléphone est requis pour les paiements mobile.',
            //     'numero_tel.regex' => 'Le numéro de téléphone doit contenir entre 10 et 15 chiffres.',
            //     'operateur_mobile.required_if' => 'Veuillez sélectionner un opérateur mobile.',
            //     'operateur_mobile.in' => 'L\'opérateur mobile sélectionné est invalide.',
            //     'numero_cheque.required_if' => 'Le numéro de chèque est requis pour les paiements par chèque.',
            //     'montant_reçu.required_if' => 'Le montant reçu est obligatoire pour les paiements en espèces.',
            //     'montant_reçu.min' => 'Le montant reçu doit être supérieur à zéro.',
            ]);
    
            // Stocker les données en session
    
    
            session(['step2' => $request->only([
                'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
                'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
                'dimension_result'
                
            ])]);
            return response()->json([
                'success' => true,
                'redirect' => route('aftlb_colis.generer.qrcode'),
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(), // Retourne les erreurs de validation sous forme de tableau associatif
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }

 
 
    


    public function complete()
    {

        return view('admin.colis.add.complete');
    }

    /**
     * Recherche automatique pour les clients.
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        $expediteurs = Client::where('type_client', 'expediteur')
            ->where(function ($query) use ($term) {
                $query->where('nom', 'LIKE', "%$term%")
                      ->orWhere('prenom', 'LIKE', "%$term%");
            })->get(['id', 'nom', 'prenom']);
        return response()->json([
            'results' => $expediteurs->map(fn($client) => [
                'id' => $client->id,
                'text' => $client->nom . ' ' . $client->prenom
            ])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = [
            'description' => $request->description,
            'quantite' => $request->quantite,
            'dimension' => $request->dimension,
            'prix' => $request->prix,
        ];
        return response()->json($product);
    }

    public function store_expediteur(Request $request)
    {
        // dd($request);
        $expediteur = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'destinataire', // Par défaut 'destinataire'
        ]);
        return redirect()->back()->with('success', 'expediteur cree avec succès !');
    }

    public function store_destinataire(Request $request)
    {
        $destinataire = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'lieu_livraison' => $request->lieu_livraison,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'expediteur', // Par défaut 'expediteur'
        ]);

        return redirect()->back()->with('success', 'Destinataire créé avec succès !');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function hold()
    {
        return view('AFT_LOUIS_BLERIOT.colis.hold');
    }

    public function dump()
    {
        return view('AFT_LOUIS_BLERIOT.colis.dump');
    }

    public function history()
    {
        return view('AFT_LOUIS_BLERIOT.colis.history');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
 
    public function edit_hold($id)
    {
        // Récupérer le colis principal avec l'ID donné
        $colis_principal = Colis::find($id);

        // Vérifier si le colis existe
        if (!$colis_principal) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
        }

        // Récupérer tous les colis qui ont la même référence que le colis principal
        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        // Vérifier si des colis correspondants ont été trouvés
        if ($colis->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }

        return view('AFT_LOUIS_BLERIOT.colis.edit_hold', compact('colis'));
    }


    public function update_hold(Request $request) // Suppression de $id ici
    {
        // Validation des données (important pour la sécurité)
        $validatedData = $request->validate([
            'colis.*.prix_transit_colis' => 'required|numeric|min:0',
            // Ajoutez d'autres règles de validation pour chaque champ modifiable.
        ]);

        $colisData = $request->input('colis');

        foreach ($colisData as $colisId => $data) {
            try {
                $colis = Colis::findOrFail($colisId);

                // Mise à jour des champs autorisés (sécurité !)
                $colis->prix_transit_colis = $data['prix_transit_colis'];
                $colis->status = 'payé';
                $colis->etat = 'Devis';
                $colis->save();

                // Reconstitution des données du QR Code (Déplacer hors de la boucle si les données ne changent pas)
                $qrData = [
                    'Référence colis' => $colis->reference_colis,
                    'Statut' => $colis->status,
                    'Nom Expéditeur' => $colis->expediteur->nom . ' ' . $colis->expediteur->prenom,
                    'Nom Destinataire' => $colis->destinataire->nom . ' ' . $colis->destinataire->prenom,
                    'Téléphone Destinataire' => $colis->destinataire->tel,
                    'Agence Destination' => $colis->destinataire->agence ?? '',
                    'Lieu de Destination' => $colis->destinataire->lieu_destination ?? '',
                ];

                // Logique du QR code ici si nécessaire (vous pouvez logguer, enregistrer, etc.)
                Log::info('QR Code Data pour le colis ' . $colisId . ': ' . json_encode($qrData));

            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
                return back()->with('error', 'Erreur lors de la mise à jour du colis ' . $colisId . ': ' . $e->getMessage());
            }
        }

        // Redirection avec un message de succès
        return redirect()->route('aftlb_colis.hold')->with('success', 'Devis faits avec succès !');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_colis(Request $request)
{
    if ($request->ajax()) {
        $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
        return DataTables::of($users)
            ->addColumn('action', function ($row) {
                $editUrl = '/users/' . $row->id . '/edit';

                return '
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-credit-card"></i>
                        </a>
                    </div>
                   
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML
            ->make(true);
    }
}


public function get_colis_hold(Request $request)
{
    if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',
                'colis.reference_colis as reference_colis',
                'expediteurs.nom as expediteur_nom', 
                'expediteurs.prenom as expediteur_prenom', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as expediteur_agence', 
                'destinataires.nom as destinataire_nom', 
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.agence as destinataire_agence', 
                'destinataires.tel as destinataire_tel',
                'colis.etat as etat',
                'colis.created_at as created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'En attente')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get()
            ->groupBy('reference_colis');
            $colisWithCount = $colis->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat === 'Devis' ? 'Dévis validé' : 'En attente',
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group->values(), // Force un tableau indexé
                ];
            })->values();
            return DataTables::of($colisWithCount)
            ->addColumn('action', function ($row) {
                $editUrl = route('aftlb_colis.hold.edit', ['id' => $row['colis']->first()->id]);
                return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modify" data-bs-target="#modifModal">
                                <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
            }
}
 
    public function get_colis_dump(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::query()
                ->select(
                    'colis.*',
                    'colis.reference_colis as reference_colis',
                    'expediteurs.nom as expediteur_nom',
                    'expediteurs.prenom as expediteur_prenom',
                    'expediteurs.tel as expediteur_tel',
                    'expediteurs.agence as expediteur_agence',
                    'destinataires.nom as destinataire_nom',
                    'destinataires.prenom as destinataire_prenom',
                    'destinataires.agence as destinataire_agence',
                    'destinataires.tel as destinataire_tel',
                    'colis.etat as etat',
                    'colis.created_at as created_at'
                )
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                ->where('etat', 'Dechargé')
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                ->get();
            $colisGrouped = $colis->groupBy('reference_colis');


            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat, // conserve l'état d'origine ici
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();

            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Dechargé' ? 'Colis validé' : $row['etat'];
                })

                

                ->make(true);
        }
    }

    // public function get_devis_colis(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $colis = Colis::select(
    //             'colis.*',  // Sélectionne toutes les colonnes de colis
    //             'colis.reference_colis as reference_colis', 
    //             'expediteurs.nom as expediteur_nom', 
    //             'expediteurs.prenom as expediteur_prenom', 
    //             'expediteurs.tel as expediteur_tel', 
    //             'expediteurs.agence as expediteur_agence', 
    //             'destinataires.nom as destinataire_nom', 
    //             'destinataires.prenom as destinataire_prenom', 
    //             'destinataires.agence as destinataire_agence', 
    //             'destinataires.tel as destinataire_tel',
    //             'colis.etat as etat',
    //             'colis.created_at as created_at'
    //         )
    //         ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
    //         ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
    //         ->whereIn('etat', ['Devis','Validé'])  // Filtre l'état des colis
    //         ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
    //         ->get(); // Exécute la requête une seule fois

    //         return DataTables::of($colis)
    //             ->addColumn('etat', function ($row) {
    //                 if ($row->etat === 'Devis') {
    //                     return 'Dévis validé'; // Si l'état est "Devis", afficher "Dévis validé"
    //                 } elseif ($row->etat === 'Validé') {
    //                     return 'Colis validé'; // Si l'état est "Validé", afficher "Colis validé"
    //                 }
    //                 return $row->etat; // Sinon, retourner l'état original
    //             })
    //             ->addColumn('action', function ($row) {
    //                $printUrl = route('aftlb_colis.qrcode.edit', ['id' => $row->id]); // Si vous avez une route d'édition pour chaque colis
                   

    //                 return '
    //                     <div class="btn-group">
    //                         <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="View">
    //                             <i class="fas fa-print"></i>
    //                         </a>
    //                     </div>
    //                 ';
    //             })
    //             ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
    //             ->make(true);
    //     }
    // }

    public function get_devis_colis(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                    'colis.id',
                    'colis.reference_colis',
                    'colis.etat',
                    'colis.created_at',
                    'expediteurs.nom as expediteur_nom', 
                    'expediteurs.prenom as expediteur_prenom', 
                    'expediteurs.tel as expediteur_tel', 
                    'expediteurs.agence as expediteur_agence', 
                    'destinataires.nom as destinataire_nom', 
                    'destinataires.prenom as destinataire_prenom', 
                    'destinataires.agence as destinataire_agence', 
                    'destinataires.tel as destinataire_tel'
                )
                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                ->whereIn('colis.etat', ['Devis', 'Validé'])
                ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
                ->get()
                ->groupBy('reference_colis');
    
            $colisWithCount = $colis->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat === 'Devis' ? 'Dévis validé' : 'Colis validé',
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('Y-m-d H:i:s') : null,
                    'colis' => $group
                ];
            })->values();
            return DataTables::of($colisWithCount)
                ->addColumn('action', function ($row) {
                    $printUrl = route('aftlb_colis.qrcode.edit', ['id' => $row['colis']->first()->id]);
                    return '
                        <div class="btn-group">
                            <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function get_colis_valide(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.id', // Ajout de l'ID du colis pour être utilisé plus tard
                'colis.reference_colis',
                'expediteurs.nom as expediteur_nom',
                'expediteurs.prenom as expediteur_prenom',
                'expediteurs.tel as expediteur_tel',
                'expediteurs.agence as expediteur_agence',
                'destinataires.nom as destinataire_nom',
                'destinataires.prenom as destinataire_prenom',
                'destinataires.agence as destinataire_agence',
                'destinataires.tel as destinataire_tel',
                'colis.etat as etat',
                'colis.created_at as created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('etat', 'Validé')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get();
            
            $colisGrouped = $colis->groupBy('reference_colis');


            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat, // conserve l'état d'origine ici
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y') : null,
                    'colis' => $group
                ];
            })->values();

            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Devis' ? 'Dévis validé' : 'Colis validé';
                })
                ->addColumn('action', function ($row) {
                    $firstColis = $row['colis']->first(); // Récupère le premier colis du groupe
                    $editUrl = route('aftlb_colis.valide.edit', ['id' => $firstColis->id]);
                    $deleteUrl = route('aftlb_colis.destroy.colis.valide', ['id' => $firstColis->id]);
                    $invoiceUrl = route('aftlb_colis.valide.edit.invoice', ['id' => $firstColis->id]);

                    return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modifier" data-bs-target="#modifModal">
                                <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                            </a>
                        </div> 
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $firstColis->id . '" data-url="' . $deleteUrl . '">
                            <i class="fas fa-trash"></i>
                        </button>
                         <a href="' . $invoiceUrl . '" class="btn btn-sm btn-primary" title="Facture" data-bs-target="#modifModal">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function editInvoice($id)
    {
        
        
        $colis_principal = Colis::find($id);
        $colis_info = Colis::with(['expediteur', 'destinataire'])
                                ->select(
                                    'colis.reference_colis',
                                    'expediteurs.nom as expediteur_nom',
                                    'expediteurs.prenom as expediteur_prenom',
                                    'expediteurs.tel as expediteur_tel',
                                    'destinataires.nom as destinataire_nom',
                                    'destinataires.prenom as destinataire_prenom',
                                    'destinataires.tel as destinataire_tel'
                                )
                                ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
                                ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
                                ->find($id);

        if (!$colis_principal) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
        }

        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        if ($colis->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }

        return view('AFT_LOUIS_BLERIOT.invoice.edit', compact('colis','colis_info'));
    }

    public function inprimerEtiquette($id)
    {
            $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colis->isEmpty()) {
                return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }       

            foreach ($colis as $colisItem) {
                   $qrData = [
                       'Référence colis'       => $colisItem->reference_colis,
                       'Statut'                => $colisItem->status,
                       'Nom Expéditeur'        => $colisItem->expediteur->nom . ' ' . $colisItem->expediteur->prenom,
                       'Nom Destinataire'      => $colisItem->destinataire->nom . ' ' . $colisItem->destinataire->prenom,
                       'Téléphone Destinataire'=> $colisItem->destinataire->tel,
                       'Agence Destination'    => $colisItem->destinataire->agence ?? '',
                       'Lieu de Destination'   => $colisItem->destinataire->lieu_destination ?? '',
                   ];
       
                   $qrCodeContent = '';
                   foreach ($qrData as $key => $value) {
                       $qrCodeContent .= "{$key}: {$value}\n";
                   }
       
                   $qrCode = new QrCode($qrCodeContent);
                   $writer = new PngWriter();
                   $result = $writer->write($qrCode);
                   $pngData = $result->getString();
       
                   $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
                   $fullPath = public_path($filePath);
       
                   $directory = dirname($fullPath);
                   if (!File::exists($directory)) {
                       File::makeDirectory($directory, 0755, true);
                   }
       
                   file_put_contents($fullPath, $pngData);
       
                   $colisItem->update(['qr_code_path' => $filePath]);
               }
       
        return view('AFT_LOUIS_BLERIOT.invoice.edit_etiquette', compact('colis'));
    }

    public function imprimerFacture($id)
    {
        
        $colis_principal = Colis::find($id);

            if (!$colis_principal) {
                return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
            }

            $colisCollection = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

            if ($colisCollection->isEmpty()) {
                return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
            }   

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
        $reference_colis = $firstColis->reference_colis;
        
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
        $nom_agent = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        // dd($nom_agent);

        // Création de la facture
        Invoice::create([
            'nom_agent' => $nom_agent,
            'nom_expediteur' => $expediteur,
            'nom_destinataire' => $destinataire,
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
        return view('AFT_LOUIS_BLERIOT.invoice.edit_invoice', compact(
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



    public function updateMultipleColis(Request $request)
    {
        // Validation globale (optionnelle, mais recommandée)
        $request->validate([
           
        ]);

        $colisData = $request->input('colis'); // Récupère toutes les données des colis

        foreach ($colisData as $colisId => $data) {
            // Récupérer le colis correspondant
            $colis = Colis::findOrFail($colisId);

            // Mise à jour des données de l'expéditeur
            $colis->expediteur->nom = $data['nom_expediteur'];
            $colis->expediteur->prenom = $data['prenom_expediteur'];
            $colis->expediteur->tel = $data['tel_expediteur'];
            $colis->expediteur->agence = $data['agence_expediteur'];
            $colis->expediteur->save();

            // Mise à jour des données du destinataire
            $colis->destinataire->nom = $data['nom_destinataire'];
            $colis->destinataire->prenom = $data['prenom_destinataire'];
            $colis->destinataire->tel = $data['tel_destinataire'];
            $colis->destinataire->agence = $data['agence_destinataire'];
            $colis->destinataire->save();

            // Mise à jour des données du colis
            $colis->quantite_colis = $data['quantite_colis'];
            $colis->valeur_colis = $data['valeur_colis'];
            $colis->mode_transit = $data['mode_transit'];
            $colis->poids_colis = $data['poids_colis'];
            $colis->prix_transit_colis = $data['prix_transit_colis'];
            $colis->save();

            // Reconstitution des données du QR Code (comme avant)
            $qrData = [
                'Référence colis' => $colis->reference_colis,
                'Statut' => $colis->status,
                'Nom Expéditeur' => $colis->expediteur->nom . ' ' . $colis->expediteur->prenom,
                'Nom Destinataire' => $colis->destinataire->nom . ' ' . $colis->destinataire->prenom,
                'Téléphone Destinataire' => $colis->destinataire->tel,
                'Agence Destination' => $colis->destinataire->agence ?? '',
                'Lieu de Destination' => $colis->destinataire->lieu_destination ?? '',
            ];

            Log::info('QR Code Data pour le colis ' . $colisId . ': ' . json_encode($qrData));

        }


        return redirect()->route('aftlb_colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
    }

    // Fonction edit pour les colis en attente

    // public function edit_colis_valide($id)
    // {
    //     $colis = Colis::findOrFail($id);
    //     // dd($colis);
    //     return view('AFT_LOUIS_BLERIOT.colis.edit_colis_valide', compact('colis'));
    // }

    public function edit_colis_valide($id)
    {
       // Vérifier si le colis existe
        $colis_principal = Colis::find($id);
        if (!$colis_principal) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Colis non trouvé.');
        }

        // Récupérer tous les colis qui ont la même référence que le colis principal
        $colis = Colis::where('reference_colis', $colis_principal->reference_colis)->get();

        // Vérifier si des colis correspondants ont été trouvés
        if ($colis->isEmpty()) {
            return redirect()->route('aftlb_colis.hold')->with('warning', 'Aucun autre colis trouvé avec cette référence.');
        }
        // dd($colis);
        return view('AFT_LOUIS_BLERIOT.colis.edit_colis_valide', compact('colis'));
    }


    public function update_colis_valide(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            // 'destinataire_agence' => 'required|string|max:255',
            // 'destinataire_tel' => 'required|string|max:255',
            // 'quantite_colis' => 'required|numeric',
            // 'valeur_colis' => 'required|numeric',
            // 'mode_transit' => 'required|string|max:255',
            // 'poids_colis' => 'required|numeric',
            // 'prix_transit_colis' => 'required|numeric',
        ]);
    
        // Récupération du colis
        $colis = Colis::findOrFail($id);
        $request->validate([
            'nom_expediteur' => 'required|string|max:255',
            'prenom_expediteur' => 'required|string|max:255',
            'destinataire_tel' => 'required|string|max:15', // Ajustez la validation selon vos besoins
            'agence_expediteur' => 'required|string|max:255',
            'nom_destinataire' => 'required|string|max:255',
            'prenom_destinataire' => 'required|string|max:255',
            'destinataire_tel' => 'required|string|max:15',
            'agence_destinataire' => 'required|string|max:255',
            'quantite_colis' => 'required|integer|min:1',
            'valeur_colis' => 'required|numeric|min:0',
            'mode_transit' => 'nullable|string|max:255',
            'poids_colis' => 'required|numeric|min:0',
            'prix_transit_colis' => 'required|numeric|min:0',
        ]);
    
        // Mise à jour des informations du colis
        $colis->update([
            'nom_expediteur' => $request->nom_expediteur,
            'prenom_expediteur' => $request->prenom_expediteur,
            'tel_expediteur' => $request->destinataire_tel,
            'agence_expediteur' => $request->agence_expediteur,
            'nom_destinataire' => $request->nom_destinataire,
            'prenom_destinataire' => $request->prenom_destinataire,
            'tel_destinataire' => $request->destinataire_tel,
            'agence_destinataire' => $request->agence_destinataire,
            'quantite_colis' => $request->quantite_colis,
            'valeur_colis' => $request->valeur_colis,
            'mode_transit' => $request->mode_transit,
            'poids_colis' => $request->poids_colis,
            'prix_transit_colis' => $request->prix_transit_colis,
        ]);
        // dd($colis);
        // Redirection avec un message de succès
        return redirect()->route('aftlb_colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
    }


    public function print_facture($id)
    {
        $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        
        // Retournez une vue pour l'impression
        return view('AFT_LOUIS_BLERIOT.colis.colis_facture', compact('colis'));
    }
    
    public function colis_valide(Request $request)
    {
        return view('AFT_LOUIS_BLERIOT.colis.valide');
    }
    // function de suppression des colis validés
    public function destroy_colis_valide($id)
    {
        // dd($id);
        try {
            $colis = Colis::findOrFail($id);
            
            $colis->delete();
            return redirect()->route('aftlb_colis.hold')->with('success', 'Colis supprimé avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('aftlb_colis.hold')->with('error', 'Une erreur est survenue lors de la suppression du colis : ' . $e->getMessage());
        }
    }



    public function edit_qrcode($id)
    {
        // Récupérer tous les colis qui appartiennent au lot (par exemple, colonne "hold_id")
        $colis = Colis::where('id', $id)->get();

        if ($colis->isEmpty()) {
            abort(404, "Aucun colis trouvé pour cet identifiant.");
        }

        // Pour chaque colis, générer le QR code
        foreach ($colis as $colisItem) {
            $qrData = [
                'Identifiant'       => $colisItem->id,
                'Référence colis'       => $colisItem->reference_colis,
                'Statut'                => $colisItem->status,
                'Nom Expéditeur'        => $colisItem->expediteur->nom . ' ' . $colisItem->expediteur->prenom,
                'Nom Destinataire'      => $colisItem->destinataire->nom . ' ' . $colisItem->destinataire->prenom,
                'Téléphone Destinataire'=> $colisItem->destinataire->tel,
                'Agence Destination'    => $colisItem->destinataire->agence ?? '',
                'Lieu de Destination'   => $colisItem->destinataire->lieu_destination ?? '',
            ];

            // Construire le contenu du QR code
            $qrCodeContent = '';
            foreach ($qrData as $key => $value) {
                $qrCodeContent .= "{$key}: {$value}\n";
            }

            // Générer le QR code
            $qrCode = new QrCode($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $pngData = $result->getString();

            // Définir le chemin du fichier QR code pour ce colis
            $filePath = 'qrcodes/colis_' . $colisItem->id . '.png';
            $fullPath = public_path($filePath);

            // Créer le répertoire si nécessaire
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Sauvegarder le QR code
            file_put_contents($fullPath, $pngData);

            // Mettre à jour le chemin du QR code dans la base de données
            $colisItem->update(['qr_code_path' => $filePath]);
        }

        return view('AFT_LOUIS_BLERIOT.devis.edit_qrcode', compact('colis'));
    }

    public function get_colis_contenaire(Request $request)
    {

        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.id', // Ajout de l'id pour éviter les erreurs
                'colis.reference_colis',
                'expediteurs.nom as expediteur_nom', 
                'expediteurs.prenom as expediteur_prenom', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as expediteur_agence', 
                'destinataires.nom as destinataire_nom', 
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.agence as destinataire_agence', 
                'destinataires.tel as destinataire_tel',
                'colis.etat',
                'colis.created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.mode_transit', 'maritime')  
            ->where('colis.etat', 'Chargé')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat,
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y H:i'): null,
                    'colis' => $group,
                    'id' => $group->first()->id // Ajout de l'ID pour action
                ];
            })->values();
    
            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Chargé' ? 'Dévis Chargé' : 'Colis Chargé';
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('colis.destroy.colis.valide', ['id' => $row['id']]);
                    return '
                       <div class="d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div> 
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function get_colis_vol(Request $request)
    {

        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.id', // Ajout de l'id pour éviter les erreurs
                'colis.reference_colis',
                'expediteurs.nom as expediteur_nom', 
                'expediteurs.prenom as expediteur_prenom', 
                'expediteurs.tel as expediteur_tel', 
                'expediteurs.agence as expediteur_agence', 
                'destinataires.nom as destinataire_nom', 
                'destinataires.prenom as destinataire_prenom', 
                'destinataires.agence as destinataire_agence', 
                'destinataires.tel as destinataire_tel',
                'colis.etat',
                'colis.created_at'
            )
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')
            ->where('colis.mode_transit', 'Aerien')
            ->where('colis.etat', 'Chargé')
            ->where('expediteurs.agence', 'AFT Agence Louis Bleriot')
            ->get();
    
            $colisGrouped = $colis->groupBy('reference_colis');
    
            $colisWithCount = $colisGrouped->map(function ($group, $reference) {
                return [
                    'reference_colis' => $reference,
                    'nombre_de_colis' => $group->count(),
                    'expediteur_nom' => $group->first()->expediteur_nom,
                    'expediteur_prenom' => $group->first()->expediteur_prenom,
                    'expediteur_tel' => $group->first()->expediteur_tel,
                    'expediteur_agence' => $group->first()->expediteur_agence,
                    'destinataire_nom' => $group->first()->destinataire_nom,
                    'destinataire_prenom' => $group->first()->destinataire_prenom,
                    'destinataire_tel' => $group->first()->destinataire_tel,
                    'destinataire_agence' => $group->first()->destinataire_agence,
                    'etat' => $group->first()->etat,
                    'created_at' => $group->first()->created_at ? $group->first()->created_at->format('d/m/Y H:i'): null,
                    'colis' => $group,
                    'id' => $group->first()->id // Ajout de l'ID pour action
                ];
            })->values();
    
            return DataTables::of($colisWithCount)
                ->addColumn('etat', function ($row) {
                    return $row['etat'] === 'Chargé' ? 'Dévis Chargé' : 'Colis Chargé';
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('colis.destroy.colis.valide', ['id' => $row['id']]);
                    return '
                       <div class="d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <a href="' . $deleteUrl . '" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div> 
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    public function get_cargaison_ferme(Request $request)
    {
        if ($request->ajax()) {
            $bateaux = Bateaux::select(
                'reference_bateau',
                'created_at as date_depart', // Création comme date de départ
                'date_arriver'
            ) ->where('agence_expedition', 'AFT Agence Louis Bleriot')
            ->get();
    
            return DataTables::of($bateaux)
                ->editColumn('date_depart', function ($row) {
                    return $row->date_depart ? \Carbon\Carbon::parse($row->date_depart)->format('d/m/Y H:i') : 'N/A';
                })
                ->editColumn('date_arriver', function ($row) {
                    return $row->date_arriver ? \Carbon\Carbon::parse($row->date_arriver)->format('d/m/Y H:i') : 'N/A';
                })
                ->make(true);
        }
    }
    

public function cargaison_ferme(Request $request)
{
 // Récupérer les agences de destination
 $agencesDestination = Agence::where('pays_agence', 'Côte d\'Ivoire')->get();

 // Récupérer les références de conteneurs fermés, sans doublons
 $referenceFermes = Colis::where('etat', 'Fermé')->pluck('reference_contenaire')->unique()->toArray();

 // Obtenir le mois et l'année actuels
 $mois = Carbon::now()->translatedFormat('F'); // Ex: Janvier, Février...
 $annee = Carbon::now()->year;

    return view('AFT_LOUIS_BLERIOT.cargaison.cargaison_ferme', compact('agencesDestination', 'referenceFermes', 'mois', 'annee'));
}
public function contenaire_fermer(Request $request)
{
    try {
        // Démarrez une transaction de base de données pour garantir l'atomicité
        DB::beginTransaction();

        $agence = 'AFT Agence Louis Bleriot'; // Définir l'agence une seule fois

        $colis = Colis::where('etat', 'Chargé')
            ->where('mode_transit', 'maritime')
            ->whereHas('expediteur', function ($query) use ($agence) {
                $query->where('agence', $agence);
            })
            ->get();

        $count = $colis->count();

        if ($count === 0) {
            return redirect()->back()->with('warning', "Aucun colis avec l'état Chargé, un mode de transit Maritime et l'agence $agence.");
        }

        // Générer une référence unique pour le conteneur
        $referenceContenaire = $this->generateReferenceContenaire();

        // Mise à jour des enregistrements
        $updatedCount = Colis::where('etat', 'Chargé')
            ->where('mode_transit', 'maritime')
            ->whereHas('expediteur', function ($query) use ($agence) {
                $query->where('agence', $agence);
            })
            ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);

        // Valider que la mise à jour a affecté le nombre attendu d'enregistrements
        if ($updatedCount !== $count) {
            DB::rollBack(); // Annulez la transaction si la mise à jour n'est pas cohérente
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour des colis. Veuillez réessayer.');
        }

        // Commit la transaction
        DB::commit();

        // Retourner un message de succès avec le nombre de colis traités
        return redirect()->back()->with('success', "$updatedCount colis de l'agence $agence ont été enregistrés dans le conteneur avec succès.");

    } catch (\Exception $e) {
        // En cas d'erreur, annuler la transaction
        DB::rollBack();
        return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
    }
}

public function vol_fermer(Request $request)
{
    try {
        // Démarrez une transaction de base de données pour garantir l'atomicité
        DB::beginTransaction();

        $agence = 'AFT Agence Louis Bleriot'; // Définir l'agence une seule fois

        $colis = Colis::where('etat', 'Chargé')
            ->where('mode_transit', 'aerien')
            ->whereHas('expediteur', function ($query) use ($agence) {
                $query->where('agence', $agence);
            })
            ->get();

        $count = $colis->count();
            // dd($count);
        if ($count === 0) {
            return redirect()->back()->with('warning', 'Aucun colis avec l’état Chargé.');
        }

        // Générer une référence unique pour le conteneur
        $referenceContenaire = $this->generateReferenceContenaire();

        // Mise à jour des enregistrements
        $updatedCount = Colis::where('etat', 'Chargé')
                    ->where('mode_transit', 'aerien')
                    ->whereHas('expediteur', function ($query) use ($agence) {
                     $query->where('agence', $agence);
        })
        ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);

        // Valider que la mise à jour a affecté le nombre attendu d'enregistrements
        if ($updatedCount !== $count) {
            DB::rollBack(); // Annulez la transaction si la mise à jour n'est pas cohérente
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour des colis. Veuillez réessayer.');
        }

        // Commit la transaction
        DB::commit();

        // Retourner un message de succès avec le nombre de colis traités
        return redirect()->back()->with('success', "$updatedCount colis ont été enregistrés dans le conteneur avec succès.");

    } catch (\Exception $e) {
        // En cas d'erreur, annuler la transaction
        DB::rollBack();
        return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
    }
}


public function devis_hold(Request $request)
{
    return view('AFT_LOUIS_BLERIOT.devis.hold');
}

public function liste_contenaire(Request $request)
{
    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());

    $agencesDestination = Agence::where('pays_agence', '=', 'Côte d\'Ivoire')->get();

    return view('AFT_LOUIS_BLERIOT.cargaison.liste_contenaire',compact('referenceContenaire','agencesDestination'));
}

public function liste_vol(Request $request)
{
    $referenceVol = $request->input('reference_vol', $this->generateReferenceVol());
    return view('AFT_LOUIS_BLERIOT.cargaison.liste_vol',compact('referenceVol'));
}
}

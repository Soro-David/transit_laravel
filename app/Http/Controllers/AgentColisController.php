<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Client;
use App\Models\Les_colis;
use App\Models\Colis;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Paiement;
use App\Models\Article;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AgentColisController extends Controller
{
    /**
     * Affiche la liste des ressources.
     */
    public function index()
    {
        // À implémenter si nécessaire
    }

    /**
     * Affiche le formulaire de création d'un colis.
     */
    public function create(Request $request)
    {
        // Récupération des agences et des clients
        $agences = Agence::select('nom_agence', 'id')->get();
        $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();

        // Redirection vers la vue
        return view('agent.colis.add', compact('agences', 'client_expediteurs', 'client_destinataires'));
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
            // Récupérer l'année et le mois actuel
            $year = now()->format('y'); // Année sur 2 chiffres
            $monthLetter = now()->format('F')[0]; // Première lettre du mois (ex: 'J' pour Janvier)
    
            // Initialiser le chiffre à 1
            $increment = 1;
    
            // Construire la référence de base
            $baseReference = "CNT-{$monthLetter}-{$increment}";
    
            // Vérifier si la référence existe déjà dans la table colis
            while (DB::table('colis')->where('reference_contenaire', $baseReference)->exists()) {
                // Incrémenter le chiffre
                $increment++;
                // Mettre à jour la référence avec le nouvel incrément
                $baseReference = "CNT-{$monthLetter}-{$increment}";
            }
    
            return $baseReference; // Retourner la référence finale
        }
    
        private function generateReferenceVol()
        {
            // Récupérer l'année et le mois actuel
            $year = now()->format('y'); // Année sur 2 chiffres
            $monthLetter = now()->format('F')[0]; // Première lettre du mois (ex: 'J' pour Janvier)
    
            // Initialiser le chiffre à 1
            $increment = 1;
    
            // Construire la référence de base
            $baseReference = "Vol-{$monthLetter}-{$increment}";
    
            // Vérifier si la référence existe déjà dans la table conteneurs
            while (DB::table('colis')->where('reference_vol', $baseReference)->exists()) {
                // Incrémenter le chiffre
                $increment++;
                // Mettre à jour la référence avec le nouvel incrément
                $baseReference = "Vol-{$monthLetter}-{$increment}";
            }
    
            return $baseReference; // Retourner la référence finale
        }
    /**
     * Étape de paiement.
     */
    // public function payement()
    // {
    //     return view('admin.colis.add.payement');
    // }

    /**
     * Étape 1 : Formulaire initial.
     */

     public function add_colis(Request $request)
    {
        
        $agences = Agence::select('nom_agence', 'id')->get();
        $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        return view('agent.colis.add_colis', compact('agences','referenceColis'));
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
                'type_embalage',
                'hauteur',
                'largeur',
                'longueur',
                'dimension_result',
                'type_colis',
                'poids',
                'description_colis',
            ])]);

            return redirect()->route('agent_colis.create.payement');

        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            \Log::error('Erreur lors de l\'enregistrement du colis : ' . $e->getMessage());

            // Retourne une réponse avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du colis. Veuillez réessayer.');
        }
    }


    public function stepPayment()
    {
       
        return view('agent.colis.add.payement');
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
                'redirect' => route('agent_colis.generer.qrcode'),
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
        // Fusionner toutes les données de session dans un tableau
        $data = array_merge(
            session('step1', []),
            session('step2', [])
        );
    // dd($data);
        // Ajouter le statut au tableau de données
        $data['status'] = $data['mode_payement'] ?? 'non payé';
        $data['etat'] = $data['etat'] ?? 'Validé';
    
        // Vérifiez que les données sont bien réparties pour chaque table
        $expediteurData = [
            'nom' => $data['nom_expediteur'],
            'prenom' => $data['prenom_expediteur'],
            'email' => $data['email_expediteur'],
            'tel' => $data['tel_expediteur'],
            'agence' => $data['agence_expedition'],
            'adresse' => $data['adresse_expediteur'],
        ];
    
        $destinataireData = [
            'nom' => $data['nom_destinataire'],
            'prenom' => $data['prenom_destinataire'],
            'email' => $data['email_destinataire'],
            'tel' => $data['tel_destinataire'],
            'agence' => $data['agence_destination'],
            'adresse' => $data['adresse_destinataire'],
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
            
            // dd($dimension_result);
            
            $colisData[] = [
                'reference_colis' => $data['reference_colis'],
                'reference_contenaire' => $data['reference_contenaire'] ?? null,
                'quantite_colis' => $quantite,
                'type_embalage' => $data['type_embalage'][$index] ?? null,
                'poids_colis' => $data['poids_colis'][$index] ?? null,
                // 'dimension_result' => $data['dimension_result'][$index] ?? null,
                'mode_transit' => $data['mode_transit'] ?? null,
                'status' => $data['status'] ?? null,
                'etat' => $data['etat'] ?? null,
                'type_colis' => $data['type_colis'][$index] ?? null,
                'dimension_result' => $dimension_result,
                'description_colis' => $data['description_colis'][$index] ?? null,
            ];
        }
        
    // dd($colisData);
        $nombreQuantiteColis = count($data['quantite_colis']);
    
        $payementData = [
            'mode_de_payement' => $data['mode_payement'],
            'montant_reçu' => $data['montant_reçu'],
            'operateur_mobile' => $data['operateur_mobile'],
            'numero_compte' => $data['numero_compte'],
            'nom_banque' => $data['nom_banque'],
            'id_transaction' => $data['transaction_id'],
            'numero_tel' => $data['numero_tel'],
            'numero_cheque' => $data['numero_cheque'],
        ];
    // dd($payementData);
        // Insérer les données dans chaque table
        $expediteur = Expediteur::create($expediteurData);
        $destinataire = Destinataire::create($destinataireData);
        $payement = Paiement::create($payementData);
    
        // Créer les colis
        $colis = [];
        foreach ($colisData as $colisItem) {
            $colis[] = Colis::create(array_merge($colisItem, [
                'expediteur_id' => $expediteur->id,
                'destinataire_id' => $destinataire->id,
                'paiement_id' => $payement->id,
            ]));
        }
    // dd($colis);
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
        return view('agent.colis.add.complete', compact('colis', 'filePath', 'fullPath', 'result'));
    }


    public function createStep1()
    {
        // Chargement des données
        // $step = 1;
        $agences = Agence::select('nom_agence', 'id')->get();
        // $client_expediteurs = Client::where('type_client', 'expediteur')->select('nom', 'prenom')->get();
        // $client_destinataires = Client::where('type_client', 'destinataire')->select('nom', 'prenom')->get();
// dd($client_destinataires);
        return view('agent.colis.add.step1',['stepProgress' => 20], compact('agences'));
    }

    /**
     * Enregistre les données de l'étape 1.
     */
    public function storeStep1(Request $request)
    {
        // dd($request->all());
        $request->session()->put('step1', $request->all());
        // Validation des données
        $request->validate([
            // Ajouter les règles de validation si nécessaires
        ]);

        // Stockage des données en session
        session(['step1' => $request->only([
            'nom_expediteur',
            'prenom_expediteur', 
            'email_expediteur', 
            'tel_expediteur',
            'agence_expedition',
            'adresse_expediteur', 
            'nom_destinataire', 
            'prenom_destinataire',
            'email_destinataire', 
            'tel_destinataire',
            'agence_destination',
            'adresse_destinataire'
        ])]);

        return redirect()->route('agent_colis.create.step2');
    }

    /**
     * Étape 2 : Détails du colis.
     */
    public function createStep2()
    {
        
        return view('agent.colis.add.step2',['stepProgress' => 40]);
    }

    public function storeStep2(Request $request)
    
    {

        $request->session()->put('step3', $request->all());
        $request->validate([
            // 'quantite' => 'required',
            // 'type_emballage' => 'required',
            // 'dimension' => 'required',
            // 'description_colis' => 'required',
            // 'poids_colis' => 'required',
            // 'valeur_colis' => 'required',
        ]);

        session(['step3' => $request->only([
           'quantite_colis',
            'type_embalage', 
            'dimension_colis', 
            'description_colis', 
            'poids_colis', 
            'valeur_colis',
        ])]);

        return redirect()->route('agent_colis.create.step3');
    }

    public function createStep3()
    {
        $step = 3;
        return view('agent.colis.add.step3',['stepProgress' => 60]);
    }
    
    public function storeStep3(Request $request)
    {
        
        $request->session()->put('step2', $request->all());
        $request->validate([
            'articles.*.description' => 'required|string|max:255',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.dimension' => 'required|string|max:255',
            'articles.*.poids' => 'required|numeric|min:0',
            
        ]);

        session(['step2' => $request->only([
            'description', 'quantite', 'dimension', 'poids'
        ])]);
        return redirect()->route('agent_colis.create.step4',['stepProgress' => 80]);
    }
    /**
     * Étape 3 : Informations de transit.
     */
    public function createStep4(Request $request)
    {
       $referenceColis = $request->input('reference_colis', $this->generateReferenceColis());
        // dd($referenceColis);
        return view('agent.colis.add.step4',['stepProgress' => 80,'referenceColis' => $referenceColis]);
    }
    public function storeStep4(Request $request)
    {
       
        $request->session()->put('step4', $request->all());
        $request->validate([
            // 'mode_transit' => 'required',
            // 'reference_colis' => 'required',
        ]);

        session(['step4' => $request->only([
            'mode_transit', 'reference_colis'
        ])]);

        return redirect()->route('agent_colis.create.payement');
    }
    /**
     * Enregistre les informations de paiement.
     */
    public function stepPayement()
    {
       
        return view('agent.colis.add.payement',['stepProgress' => 100]);
    }
    public function storePayement(Request $request)
    {

        $request->validate([
            // 'mode_payement' => 'required|in:bank,mobile_money,cheque,cash',
            // // Validation pour le paiement bancaire
            // 'numero_compte' => 'required_if:mode_payement,bank|max:255',
            // 'nom_banque' => 'required_if:mode_payement,bank|max:255',
            // 'transaction_id' => 'required_if:mode_payement,bank,mobile_money|max:255',
        
            // // Validation pour Mobile Money
            // 'tel' => 'required_if:mode_payement,mobile_money|regex:/^\d{10,15}$/',
            // 'operateur' => 'required_if:mode_payement,mobile_money|in:mtn,orange,airtel',
            // // Validation pour le paiement par chèque
            // 'numero_cheque' => 'required_if:mode_payement,cheque|max:255',
            // 'nom_banque' => 'required_if:mode_payement,cheque|max:255',
            // // Validation pour le paiement en espèces
            // 'montant_reçu' => 'required_if:mode_payement,cash|numeric|min:1',
        ]);

        session(['step5' => $request->only([
            'mode_payement', 'numero_compte', 'nom_banque', 'transaction_id', 
            'numero_tel', 'operateur_mobile', 'numero_cheque', 'montant_reçu',
        ])]);

        return response()->json([
            'success' => true,
            'redirect' => route('agent_colis.create.qrcode'),
        ]);
        
    }

    public function qrcode(Request $request)
    {
        // Fusionner toutes les données de session dans un tableau
        $data = array_merge(
            session('step1', []),
            session('step2', []),
            session('step3', []),
            session('step4', []),
            session('step5', [])
        );
    
         // Ajouter le statut au tableau de données
         $data['status'] = $data['mode_payement'] ?? 'non payé';
         $data['etat'] = $data['etat'] ?? 'Validé';
 
 
         // Vérifiez que les données sont bien réparties pour chaque table
         $expediteurData = [
             'nom' => $data['nom_expediteur'] ,
             'prenom' => $data['prenom_expediteur'] ,
             'email' => $data['email_expediteur'] ,
             'tel' => $data['tel_expediteur'] ,
             'agence' => $data['agence_expedition'] ,
             'adresse' => $data['adresse_expediteur'] ,
         ];
 
 
         $destinataireData = [
             'nom' => $data['nom_destinataire'] ,
             'prenom' => $data['prenom_destinataire'] ,
             'email' => $data['email_destinataire'] ,
             'tel' => $data['tel_destinataire'] ,
             'agence' => $data['agence_destination'] ,
             'adresse' => $data['adresse_destinataire'] ,
         ];
 
         // Vérification que les tableaux nécessaires existent et sont synchronisés
     if (
         isset($data['description'], $data['quantite'], $data['dimension'], $data['poids']) &&
         is_array($data['description']) &&
         is_array($data['quantite']) &&
         is_array($data['dimension']) &&
         is_array($data['poids']) &&
         count($data['description']) === count($data['quantite']) &&
         count($data['quantite']) === count($data['dimension']) &&
         count($data['dimension']) === count($data['poids'])
     ) {
         // Construction du tableau d'articles
         $articleData = [];
         foreach ($data['description'] as $index => $description) {
             $articleData[] = [
                 'description' => $description,
                 'quantite' => $data['quantite'][$index],
                 'dimension' => $data['dimension'][$index],
                 'poids' => $data['poids'][$index],
             ];
         }
     
 
         // Afficher ou traiter $articleData
         // dd($articleData); // Affiche les données sous forme de tableau structuré
     } else {
         // Gestion d'erreur
         throw new \Exception('Les données des articles sont manquantes ou incohérentes.');
     }
 
         $colisData = [
             'reference_colis' => $data['reference_colis'] ,
             'reference_contenaire' => $data['reference_contenaire'] ?? null,
             'quantite_colis' => $data['quantite_colis'] ,
             'type_embalage' => $data['type_embalage'] ,
             'valeur_colis' => $data['valeur_colis'],
             'poids_colis' => $data['poids_colis'] ,
             'dimension_result' => $data['dimension_result'] ,
             'mode_transit' => $data['mode_transit'] ,
             'status' => $data['status'],
             'etat' => $data['etat'],
         ];
         $payementData = [
             'mode_de_payement' => $data['mode_payement'] ,
             'montant_reçu' => $data['montant_reçu'] ,
             'operateur_mobile' => $data['operateur_mobile'] ,
             'numero_compte' => $data['numero_compte'],
             'nom_banque' => $data['nom_banque'] ,
             'id_transaction' => $data['transaction_id'],
             'numero_tel' => $data['numero_tel'],
             'numero_cheque' => $data['numero_cheque'] ,
         ];
 
         // dd($expediteurData, $destinataireData, $articleData, $colisData, $payementData);
 
 
         // Insérer les données dans chaque table
         $expediteur = Expediteur::create($expediteurData);
         $destinataire = Destinataire::create($destinataireData);
         // $article = Article::create($articleData);
         $payement = Paiement::create($payementData);
         $colis = Colis::create(array_merge($colisData, [
             'expediteur_id' => $expediteur->id,
             'destinataire_id' => $destinataire->id,
             'paement_id' => $payement->id,
         ]));
         foreach ($articleData as $article) {
             $article['colis_id'] = $colis->id;
             Article::create($article);
         }
         
         // dd($article);
 
         
 
     // dd($colis );
 
         // Format lisible pour le QR code
         $qrData = [
             'Référence colis' => $data['reference_colis'],
             'Statut' => $colis->status,
             'Nom Expéditeur' => $data['nom_expediteur'] . ' ' . $data['prenom_expediteur'],
             'Nom Destinataire' => $data['nom_destinataire'] . ' ' . $data['prenom_destinataire'],
             'Téléphone Destinataire' => $data['tel_destinataire'],
             'Agence Destination' => $data['agence_destination'] ?? '',
             'Lieu de Destination' => $data['lieu_destination'] ?? '',
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
         $filePath = 'qrcodes/colis_' . $colis->id . '.png';
         $fullPath = public_path($filePath);
 
         // Vérifier et créer le répertoire cible si nécessaire
         $directory = dirname($fullPath);
         if (!File::exists($directory)) {
             File::makeDirectory($directory, 0755, true);
         }
 
         // Sauvegarder le fichier QR code dans le storage
         file_put_contents($fullPath, $pngData);
 
         // Mettre à jour le chemin du QR code dans la base de données
         $colis->update(['qr_code_path' => $filePath]);
 
         // Réinitialiser les sessions après traitement
         session()->forget(['step1', 'step2', 'step3', 'step4', 'step5']);
        // Retourner la vue avec les informations nécessaires
        return view('agent.colis.add.complete', compact('colis', 'filePath', 'fullPath', 'result'));
    }


    



    

    
    /**
     * Étape finale : Confirmation.
     */
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
        return view('agent.colis.hold');
    }

    public function dump()
    {
        return view('agent.colis.dump');
    }

    public function history()
    {
        return view('admin.colis.history');
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
        // dd($id);
        $colis = Colis::findOrFail($id);
        // dd($colis);
        return view('agent.colis.edit_hold', compact('colis'));
    }

    // Fonction update pour les colis en attente
    public function update_hold(Request $request, $id)
    {
    // dd($request->all());

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
        // Mise à jour des champs
        $colis->update([
            'destinataire_agence' => $request->input('destinataire_agence'),
            'destinataire_tel' => $request->input('destinataire_tel'),
            'quantite_colis' => $request->input('quantite_colis'),
            'valeur_colis' => $request->input('valeur_colis'),
            'mode_transit' => $request->input('mode_transit'),
            'poids_colis' => $request->input('poids_colis'),
            'prix_transit_colis' => $request->input('prix_transit_colis'),
            'status' => 'payé', // Ajout du statut
            'etat' => 'Devis', // Ajout du statut
        ]);
        // Redirection avec un message de succès
        return redirect()->route('agent_colis.hold')->with('success', 'Colis mis à jour avec succès !');
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
            'colis.*',  // Sélectionne toutes les colonnes de colis
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
        ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
        ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
        ->where('etat', 'En attente')  // Filtre l'état des colis
        ->get(); // Exécute la requête une seule fois

        return DataTables::of($colis)
            ->addColumn('action', function ($row) {
                $editUrl = route('agent_colis.hold.edit', ['id' => $row->id]); // Si vous avez une route d'édition pour chaque colis

                return '
                    <div class="btn-group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modify" data-bs-target="#modifModal">
                            <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
            ->make(true);
    }
}
    // Ajax pour les colis arrivés
    public function get_colis_dump(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'Dechargé')  // Filtre l'état des colis
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('action', function ($row) {
                    $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }

    public function get_devis_colis(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->whereIn('etat', ['Devis','Validé'])  // Filtre l'état des colis
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('etat', function ($row) {
                    if ($row->etat === 'Devis') {
                        return 'Dévis validé'; // Si l'état est "Devis", afficher "Dévis validé"
                    } elseif ($row->etat === 'Validé') {
                        return 'Colis validé'; // Si l'état est "Validé", afficher "Colis validé"
                    }
                    return $row->etat; // Sinon, retourner l'état original
                })
                ->addColumn('action', function ($row) {
                   $printUrl = route('agent_colis.qrcode.edit', ['id' => $row->id]); // Si vous avez une route d'édition pour chaque colis
                   

                    return '
                        <div class="btn-group">
                            <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }

    public function get_colis_valide(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'Validé')  // Filtre l'état des colis
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('etat', function ($row) {
                    if ($row->etat === 'Validé') {
                        return 'Colis validé'; // Si l'état est "Validé", afficher "Colis validé"
                    } 
                    return $row->etat; // Sinon, retourner l'état original
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('agent_colis.valide.edit', ['id' => $row->id]);
                    $deleteUrl = route('agent_colis.destroy.colis.valide', ['id' => $row->id]);
                    $printUrl = route('agent_colis.facture.colis.print', ['id' => $row->id]);
                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning d-flex justify-content-center align-items-center" title="Modifier" data-bs-target="#modifModal">
                                <i class="fas fa-credit-card" style="font-size: 15px;"></i>
                            </a>
                                <a href="' . $printUrl . '" class="btn btn-sm btn-info" title="Imprimer" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                        </div> 
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }

    // Fonction edit pour les colis en attente

    public function edit_colis_valide($id)
    {
        $colis = Colis::findOrFail($id);
        // dd($colis);
        return view('agent.colis.edit_colis_valide', compact('colis'));
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
        return redirect()->route('agent_colis.colis.valide')->with('success', 'Colis mis à jour avec succès !');
    }


    public function print_facture($id)
    {
        $colis = Colis::with(['expediteur', 'destinataire'])->findOrFail($id);
        
        // Retournez une vue pour l'impression
        return view('agent.colis.colis_facture', compact('colis'));
    }
    
    public function colis_valide(Request $request)
    {
        return view('agent.colis.valide');
    }
    // function de suppression des colis validés
    public function destroy_colis_valide($id)
    {
        // dd($id);
        try {
            $colis = Colis::findOrFail($id);
            
            $colis->delete();
            return redirect()->route('agent_colis.hold')->with('success', 'Colis supprimé avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('agent_colis.hold')->with('error', 'Une erreur est survenue lors de la suppression du colis : ' . $e->getMessage());
        }
    }


    // public function edit_qrcode($id)
    // {
    //     $colis = Colis::findOrFail($id);

    //     $qrData = [
    //         'Référence colis' => $colis->reference_colis,
    //         'Statut' => $colis->status,
    //         'Nom Expéditeur' => $colis->expediteur->nom. ' ' . $colis->expediteur->prenom,
    //         'Nom Destinataire' =>  $colis->destinataire->nom . ' ' . $colis->destinataire->prenom,
    //         'Téléphone Destinataire' => $colis->destinataire->tel,
    //         'Agence Destination' => $colis->destinataire->agence ?? '',
    //         'Lieu de Destination' => $colis->destinataire->lieu_destination ?? '',
    //     ];
    //     // dd($qrData);
    //      // Construire le contenu du QR code
    //      $qrCodeContent = '';
    //      foreach ($qrData as $key => $value) {
    //          $qrCodeContent .= "{$key}: {$value}\n";
    //      }
 
    //      // Générer le QR code
    //      $qrCode = new QrCode($qrCodeContent);
    //      $writer = new PngWriter();
    //      $result = $writer->write($qrCode);
    //      $pngData = $result->getString();
 
    //      // Définir le chemin du fichier QR code
    //      $filePath = 'qrcodes/colis_' . $colis->id . '.png';
    //      $fullPath = public_path($filePath);
 
    //      // Vérifier et créer le répertoire cible si nécessaire
    //      $directory = dirname($fullPath);
    //      if (!File::exists($directory)) {
    //          File::makeDirectory($directory, 0755, true);
    //      }
 
    //      // Sauvegarder le fichier QR code dans le storage
    //      file_put_contents($fullPath, $pngData);
 
    //      // Mettre à jour le chemin du QR code dans la base de données
    //      $colis->update(['qr_code_path' => $filePath]);
        
    //     return view('agent.devis.edit_qrcode', compact('colis','filePath'));
    // }


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

        return view('agent.devis.edit_qrcode', compact('colis'));
    }

    public function get_colis_contenaire(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table users pour expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table users pour destinataires
            ->where('etat', 'Chargé')  // Filtre l'état des colis
            ->get(); // Exécute la requête une seule fois

            return DataTables::of($colis)
                ->addColumn('action', function ($row) {
                    $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }

    public function get_colis_vol(Request $request)
    {
        if ($request->ajax()) {
            $colis = Colis::select(
                'colis.*',  // Sélectionne toutes les colonnes de colis
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
            ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
            ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table destinataires
            ->where('colis.mode_transit', 'Aerien')  // Filtre pour le mode de transit
            ->where('colis.etat', 'Chargé')  // Filtre l'état des colis
            ->get(); // Exécute la requête 

            return DataTables::of($colis)
                ->addColumn('action', function ($row) {
                    $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

                    return '
                        <div class="btn-group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
                ->make(true);
        }
    }
    // public function get_colis_contenaire(Request $request)
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
    //         ->join('expediteurs', 'colis.expediteur_id', '=', 'expediteurs.id')  // Jointure avec la table expediteurs
    //         ->join('destinataires', 'colis.destinataire_id', '=', 'destinataires.id')  // Jointure avec la table destinataires
    //         ->where('colis.mode_transit', 'maritime')  // Filtre pour le mode de transit
    //         ->where('colis.etat', 'Chargé')  // Filtre l'état des colis
    //         ->get(); // Exécute la requête 

    //         return DataTables::of($colis)
    //             ->addColumn('action', function ($row) {
    //                 $editUrl = '/users/' . $row->id . '/edit'; // Si vous avez une route d'édition pour chaque colis

    //                 return '
    //                     <div class="btn-group">
    //                         <a href="' . $editUrl . '" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#showModal">
    //                             <i class="fas fa-eye"></i>
    //                         </a>
    //                         <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
    //                             <i class="fas fa-credit-card"></i>
    //                         </a>
    //                     </div>
    //                 ';
    //             })
    //             ->rawColumns(['action']) // Permet de rendre le HTML dans la colonne "action"
    //             ->make(true);
    //     }
    // }

public function cargaison_ferme(Request $request)
{
    return view('agent.cargaison.cargaison_ferme');
}
public function contenaire_fermer(Request $request)
{

try {
    // Compter les enregistrements avant la mise à jour
    $count = Colis::where('etat', 'Chargé')->count();
    if ($count === 0) {
        return redirect()->back()->with('warning', 'Aucun colis avec l’état "validé" trouvé.');
    }
     // Générer une référence unique pour le conteneur
     $referenceContenaire = 'CNT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(3));
    // Mise à jour des enregistrements
    $colisData = Colis::where('etat', 'Chargé')
        ->update(['etat' => 'Fermé', 'reference_contenaire' => $referenceContenaire]);

    return redirect()->back()->with('success', "$colisData colis sont enregistrer dans le conteneur $referenceContenaire avec succès.");
} catch (\Exception $e) {
    return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
}
}



public function devis_hold(Request $request)
{
    return view('agent.devis.hold');
}

public function liste_contenaire(Request $request)
{
    $referenceContenaire = $request->input('reference_contenaire', $this->generateReferenceContenaire());
    return view('agent.devis.liste_contenaire',compact('referenceContenaire'));
}

public function liste_vol(Request $request)
{
    $referenceVol = $request->input('reference_vol', $this->generateReferenceVol());
    return view('agent.cargaison.liste_vol',compact('referenceVol'));
}
}

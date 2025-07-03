<?php
namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Programme;
use Illuminate\Http\Request;
use App\Models\Colis;
use Illuminate\Support\Facades\DB;

class ProgrammeLBController extends Controller
{
    public function index()
    {
        return view('AFT_LOUIS_BLERIOT.transport.planing');
    }

    public function data()
    {
        // Récupérer les chauffeurs de l'agence 5 (Louis Blériot)
        $chauffeurs = Chauffeur::where('agence_id', 5)->get();
        
        // Récupérer les programmes associés aux chauffeurs de l'agence 5
        $programmes = Programme::whereHas('chauffeur', function ($query) {
                $query->where('agence_id', 5); // Filtre crucial par agence
            })
            ->with('chauffeur')
            ->orderByDesc('date_programme')
            ->get()
            ->map(function ($programme) {
                // Mapping des champs pour correspondre au format attendu par le frontend
                return [
                    'id' => $programme->id,
                    'date_programme' => $programme->date_programme,
                    'chauffeur' => $programme->chauffeur,
                    'reference_colis' => $programme->reference_colis,
                    'nature_du_colis' => $programme->nature_du_colis,
                    'actions_a_faire' => $programme->actions_a_faire,
                    'nom_expediteur' => $programme->nom_expediteur,
                    'Adresse_expedition' => $programme->lieu_expedition, // Mapping correct
                    'tel_expediteur' => $programme->tel_expediteur,
                    'nom_destinataire' => $programme->nom_destinataire,
                    'tel_destinataire' => $programme->tel_destinataire,
                    'Adresse_destination' => $programme->lieu_destination, // Mapping correct
                    'etat_rdv' => $programme->etat_rdv
                ];
            });
    
        // Récupérer les colis valides non attribués
        $colisValides = Colis::where('etat', 'Validé')
            ->whereNotIn('reference_colis', Programme::pluck('reference_colis')->toArray())
            ->with('expediteur', 'destinataire')
            ->get();
    
        return response()->json([
            'chauffeurs' => $chauffeurs,
            'programmes' => $programmes,
            'colisValides' => $colisValides
        ]);
    }
    public function storeProgramme(Request $request)
    {
        \Log::info("Debut de la creation du programme");
        try {
            // Supprimer la validation exists pour reference_colis
            $request->validate([
                'date_programme' => 'required|date',
                'chauffeur_id' => 'required|exists:chauffeurs,id',
                'actions_a_faire.*' => 'required|in:depot,recuperation,livraison',
            ]);
    
            $dateProgramme = $request->date_programme;
            $chauffeurId = $request->chauffeur_id;
            $referencesColis = $request->input('reference_colis', []);
        $natureDuColis = $request->input('nature_du_colis', []);
            $actionsAFaire = $request->input('actions_a_faire', []);
            $nomExpediteurs = $request->input('nom_expediteur', []);
            $adresseExpeditions = $request->input('Adresse_expedition', []);
            $telExpediteurs = $request->input('tel_expediteur', []);
            $nomDestinataires = $request->input('nom_destinataire', []);
            $telDestinataires = $request->input('tel_destinataire', []);
            $adresseDestinations = $request->input('Adresse_destination', []);
    
            DB::beginTransaction();
    
            foreach ($actionsAFaire as $index => $action) {
                $referenceColis = $referencesColis[$index] ?? null;
     // MODIFICATION 2: Vérifier l'unicité pour tous les types d'actions
     if (!empty($referenceColis)) {
        $programmeExistant = Programme::where('reference_colis', $referenceColis)->first();
        if ($programmeExistant) {
            throw new \Exception("Le colis $referenceColis est déjà attribué");
        }
    }
                if ($action === 'recuperation') {
                    // MODIFICATION 1: Supprimer la validation des champs pour la récupération
                    Programme::create([
                        'date_programme' => $dateProgramme,
                        'chauffeur_id' => $chauffeurId,
                        'reference_colis' => $referenceColis, // Peut être null
                        'nature_du_colis' => $natureDuColis[$index] ?? null,
                        'actions_a_faire' => $action,
                        'nom_expediteur' => $nomExpediteurs[$index] ?? null,
                        'lieu_expedition' => $adresseExpeditions[$index] ?? null,
                        'tel_expediteur' => $telExpediteurs[$index] ?? null,
                        'nom_destinataire' => $nomDestinataires[$index] ?? null,
                        'tel_destinataire' => $telDestinataires[$index] ?? null,
                        'lieu_destination' => $adresseDestinations[$index] ?? null,
                        'etat_rdv' => 'en attente',
                    ]);
                } else {
                    // Validation pour les autres actions
                    if (empty($referenceColis)) {
                        throw new \Exception("La référence colis est obligatoire pour l'action '$action'");
                    }
    
                    $programmeExistant = Programme::where('reference_colis', $referenceColis)->first();
                    if ($programmeExistant) {
                        throw new \Exception("Le colis $referenceColis est déjà attribué");
                    }
    
                    $colis = Colis::where('reference_colis', $referenceColis)->with('expediteur', 'destinataire')->first();
                    if (!$colis) {
                        throw new \Exception("Le colis $referenceColis n'existe pas");
                    }
    
                    $nomExpediteur = $colis->expediteur->nom . ' ' . $colis->expediteur->prenom;
                    $nomDestinataire = $colis->destinataire->nom . ' ' . $colis->destinataire->prenom;
    
                    Programme::create([
                        'date_programme' => $dateProgramme,
                        'chauffeur_id' => $chauffeurId,
                        'reference_colis' => $referenceColis,
                        'nature_du_colis' => $natureDuColis[$index] ?? null,
                        'actions_a_faire' => $action,
                        'nom_expediteur' => $nomExpediteur,
                        'lieu_expedition' => $colis->expediteur->adresse,
                        'tel_expediteur' => $colis->expediteur->tel,
                        'nom_destinataire' => $nomDestinataire,
                        'tel_destinataire' => $colis->destinataire->tel,
                        'lieu_destination' => $colis->destinataire->adresse,
                        'etat_rdv' => 'en attente',
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Programmes créés avec succès!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur création programmes: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function edit($id)// Modifié pour utiliser l'ID
{
    // Vérifier que le programme appartient à l'agence 5
    $programme = Programme::whereHas('chauffeur', fn($q) => $q->where('agence_id', 5))
        ->findOrFail($id);

    $chauffeurs = Chauffeur::where('agence_id', 5)->get();
    
    return response()->json([
        'programme' => $programme,
        'chauffeurs' => $chauffeurs
    ]);
}
public function update(Request $request, $id)
{
    $programme = Programme::whereHas('chauffeur', fn($q) => $q->where('agence_id', 5))
        ->findOrFail($id);

    // Déterminer l'action
    $action = $request->has('actions_a_faire') 
        ? $request->actions_a_faire 
        : $programme->actions_a_faire;

    $rules = [
        'date_programme' => 'nullable|date',
        'chauffeur_id' => 'nullable|exists:chauffeurs,id',
        'actions_a_faire' => 'nullable|in:depot,recuperation,livraison',
    ];

    // Règle conditionnelle pour reference_colis
    if ($action === 'recuperation') {
        $rules['reference_colis'] = 'nullable';
    } else {
        $rules['reference_colis'] = 'nullable|exists:colis,reference_colis';
    }
    if ($request->has('nature_du_colis')) {
        $programme->nature_du_colis = $request->nature_du_colis;
        $updated = true;
    }
    $request->validate($rules);

    // Vérification de la référence seulement pour les actions non-récupération
    if ($action !== 'recuperation' && 
        $request->has('reference_colis') && 
        $request->reference_colis != $programme->reference_colis) 
    {
        $programmeExistant = Programme::where('reference_colis', $request->reference_colis)
            ->where('id', '!=', $programme->id)
            ->first();

        if ($programmeExistant) {
            return response()->json([
                'success' => false,
                'message' => "Le colis {$request->reference_colis} est déjà attribué"
            ], 422);
        }
    }

    // Mettre à jour les champs modifiés
    $updated = false;
    $fields = ['date_programme', 'chauffeur_id', 'reference_colis', 'actions_a_faire'];
    
    foreach ($fields as $field) {
        if ($request->has($field) && $request->$field != $programme->$field) {
            $programme->$field = $request->$field;
            $updated = true;
        }
    }

    // Sauvegarder uniquement si des modifications ont été apportées
    if ($updated) {
        $programme->save();
        return response()->json([
            'success' => true,
            'message' => 'Programme mis à jour avec succès'
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Aucune modification détectée'
    ]);
}
    public function destroy(Programme $programme)
    {
        try {
            $programme->delete();
            return response()->json(['success' => true, 'message' => 'Programme supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
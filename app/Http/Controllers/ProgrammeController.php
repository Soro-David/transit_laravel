<?php
namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Programme;
use Illuminate\Http\Request;
use App\Models\Colis;
use PDF;
use Illuminate\Support\Facades\DB;

class ProgrammeController extends Controller
{
    public function index()
    {
        // $chauffeurs = Chauffeur::all();
        // dd($chauffeurs);
        return view('admin.Programme.programme');
    }

    public function data()
    {
        $chauffeurs = Chauffeur::all();
        $programmes = Programme::with('chauffeur')->orderByDesc('date_programme')->get()->map(function ($programme) {
            $programme->Adresse_expedition = $programme->lieu_expedition;
            $programme->Adresse_destination = $programme->lieu_destination;
            return $programme;
        });
     // Récupérer tous les colis valides
    $colisValidesQuery = Colis::where('etat', 'Validé')->with('expediteur', 'destinataire');

    // Récupérer les références de colis déjà attribuées
    $colisAttribues = Programme::pluck('reference_colis')->toArray();

    // Exclure les colis déjà attribués de la requête
    $colisValidesQuery->whereNotIn('reference_colis', $colisAttribues);

    // Récupérer les colis valides filtrés
    $colisValides = $colisValidesQuery->get();

        return response()->json(['chauffeurs' => $chauffeurs, 'programmes' => $programmes, 'colisValides' => $colisValides]);
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

    public function edit(Programme $programme) // Étape 1: Utiliser le Route-Model Binding de Laravel
    {
        // Étape 2: Charger la relation avec le chauffeur pour être sûr qu'elle est incluse dans le JSON
        $programme->load('chauffeur');
        
        // Étape 3: Récupérer la liste de TOUS les chauffeurs pour le menu déroulant du modal
        $chauffeurs = Chauffeur::all();
        
        // Étape 4: Retourner la réponse JSON avec les variables maintenant définies
        return response()->json([
            'programme' => $programme,
            'chauffeurs' => $chauffeurs
        ]);
    }

    public function update(Request $request, Programme $programme)
{
    $rules = [
        'date_programme' => 'nullable|date',
        'chauffeur_id' => 'nullable|exists:chauffeurs,id', 
        'actions_a_faire' => 'nullable|in:depot,recuperation,livraison',
    ];

    $request->validate($rules);

    // Vérifier si la référence du colis a été modifiée
    if ($request->has('reference_colis') && $request->reference_colis != $programme->reference_colis) {
        $programmeExistant = Programme::where('reference_colis', $request->reference_colis)
            ->where('id', '!=', $programme->id)
            ->first();

        if ($programmeExistant) {
            return response()->json([
                'success' => false,
                'message' => "Le colis avec la référence {$request->reference_colis} est déjà attribué"
            ], 422);
        }
    }

    // Mettre à jour uniquement les champs fournis
    $updated = false;
    if ($request->has('date_programme') && $request->date_programme != $programme->date_programme) {
        $programme->date_programme = $request->date_programme;
        $updated = true;
    }
    if ($request->has('chauffeur_id') && $request->chauffeur_id != $programme->chauffeur_id) {
        $programme->chauffeur_id = $request->chauffeur_id;
        $updated = true;
    }
    
    if ($request->has('actions_a_faire') && $request->actions_a_faire != $programme->actions_a_faire) {
        $programme->actions_a_faire = $request->actions_a_faire;
        $updated = true;
    }

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
    public function exportPDF()
{
    $programmes = Programme::with('chauffeur')
        ->orderByDesc('date_programme')
        ->get()
        ->map(function ($programme) {
            $programme->Adresse_expedition = $programme->lieu_expedition;
            $programme->Adresse_destination = $programme->lieu_destination;
            return $programme;
        });

    $pdf = PDF::loadView('admin.programme.pdf', compact('programmes'));
    return $pdf->download('programmes-list.pdf');
}
}
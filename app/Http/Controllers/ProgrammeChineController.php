<?php
namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Programme;
use Illuminate\Http\Request;
use App\Models\Colis;
use Illuminate\Support\Facades\DB;

class ProgrammeChineController extends Controller
{
    public function index()
    {
        return view('AGENCE_CHINE.transport.planing');
    }

    public function data()
    {
        // Modifier la requête pour filtrer les chauffeurs par agence_id = 7 pour la chine
        $chauffeurs = Chauffeur::where('agence_id', 7)->get();
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
            $request->validate([
                'date_programme' => 'required|date',
                'chauffeur_id' => 'required|exists:chauffeurs,id',
                'reference_colis.*' => 'nullable|exists:colis,reference_colis',
                'actions_a_faire.*' => 'nullable|in:depot,recuperation',
            ]);

            $dateProgramme = $request->date_programme;
            $chauffeurId = $request->chauffeur_id;
            $referencesColis = $request->input('reference_colis');
            $actionsAFaire = $request->input('actions_a_faire');

            DB::beginTransaction();

            foreach ($referencesColis as $index => $referenceColis) {
                if ($referenceColis) {
                    // Vérifier si le colis est déjà attribué à un autre chauffeur
                    $programmeExistant = Programme::where('reference_colis', $referenceColis)->first();

                    if ($programmeExistant) {
                        DB::rollBack();
                        return redirect()->back()->with('error', "Le colis avec la référence {$referenceColis} est déjà attribué à un autre chauffeur.");
                    }

                    $colis = Colis::where('reference_colis', $referenceColis)->with('expediteur', 'destinataire')->first();

                    if ($colis) {
                        // Récupération du nom complet (nom + prénom)
                        $nomExpediteur = $colis->expediteur->nom . ' ' . $colis->expediteur->prenom;
                        $nomDestinataire = $colis->destinataire->nom . ' ' . $colis->destinataire->prenom;

                        Programme::create([
                            'date_programme' => $dateProgramme,
                            'chauffeur_id' => $chauffeurId,
                            'reference_colis' => $referenceColis,
                            'actions_a_faire' => $actionsAFaire[$index] ?? null,
                            'nom_expediteur' => $nomExpediteur,
                            'lieu_expedition' => $colis->expediteur->adresse,
                            'tel_expediteur' => $colis->expediteur->tel,
                            'nom_destinataire' => $nomDestinataire,
                            'tel_destinataire' => $colis->destinataire->tel,
                            'lieu_destination' => $colis->destinataire->adresse,
                            'etat_rdv' => 'en attente', // Valeur par défaut pour le nouvel état
                        ]);
                    }
                }
            }

            DB::commit();

            \Log::info("Programmes créés avec succès pour le chauffeur ID : " . $chauffeurId . " à la date : " . $dateProgramme);
            return redirect()->back()->with('success', 'Programmes créés avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur lors de la création des programmes : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création des programmes : ' . $e->getMessage());
        }
    }

    public function edit(Programme $programme)
    { // Modifier la requête pour filtrer les chauffeurs par agence_id = 7 pour la chine
        $chauffeurs = Chauffeur::where('agence_id', 7)->get();
        return response()->json(['programme' => $programme, 'chauffeurs' => $chauffeurs]);
    }

    public function update(Request $request, Programme $programme)
    {
        $rules = [
            'date_programme' => 'nullable|date', // Rendre nullable si on veut modifier que le RDV.
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'reference_colis' => 'nullable|exists:colis,reference_colis', // rendre nullable
            'actions_a_faire' => 'nullable|in:depot,recuperation', //rendre nullable
        ];
    
        $request->validate($rules);
    
        // Vérifier si la référence du colis a été modifiée
        if ($request->has('reference_colis') && $request->reference_colis != $programme->reference_colis) {
            // Vérifier si le colis est déjà attribué à un autre chauffeur (sauf le programme actuel)
            $programmeExistant = Programme::where('reference_colis', $request->reference_colis)
                ->where('id', '!=', $programme->id) // Exclure le programme actuel
                ->first();
    
            if ($programmeExistant) {
                return redirect()->back()->with('error', "Le colis avec la référence {$request->reference_colis} est déjà attribué à un autre chauffeur.");
            }
        }
    
        // Mettre à jour uniquement les champs fournis dans la requête
        if ($request->has('date_programme')) {
            $programme->date_programme = $request->date_programme;
        }
        if ($request->has('chauffeur_id')) {
            $programme->chauffeur_id = $request->chauffeur_id;
        }
    
        if ($request->has('reference_colis')) {
            $programme->reference_colis = $request->reference_colis;
        }
    
        if ($request->has('actions_a_faire')) {
            $programme->actions_a_faire = $request->actions_a_faire;
        }
        $programme->save();
    
        return redirect()->back()->with('success', 'Programme mis à jour avec succès!');
    }

    public function destroy(Programme $programme)
    {
        $programme->delete();
        return redirect()->back()->with('success', 'Programme supprimé avec succès!');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Agence;
use App\Models\Chauffeur;
use App\Models\Colis;
use App\Models\Destinataire;
use App\Models\Expediteur;
use App\Models\Programme;


class AgentIPMSANGRETransportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('IPMS_SIMEXCI_ANGRE.transport.index');
    }

    public function show_chauffeur()
{
    // Récupérer les agences dont le pays est France
    $agences = Agence::where('pays_agence', 'Côte d\'Ivoire')->select('nom_agence', 'id')->get();

    return view('IPMS_SIMEXCI_ANGRE.transport.chauffeur', compact('agences'));
}

public function planing_chauffeur()
{
    // Ajout du filtre Côte d'Ivoire
    $agences = Agence::where('pays_agence', 'Côte d\'Ivoire')
                   ->select('nom_agence', 'id')
                   ->get();

    $chauffeurs = Chauffeur::select('nom','prenom','id')->get();

    return view('IPMS_SIMEXCI_ANGRE.transport.planing', compact('agences','chauffeurs'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
// Dans le modèle Chauffeur
public function agence()
{
    return $this->belongsTo(Agence::class, 'agence_id'); // Correction du nom de colonne
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $chauffeur = Chauffeur::findOrFail($id);

        // Ajout du filtre Côte d'Ivoire
        $agences = Agence::where('pays_agence', 'Côte d\'Ivoire')
                       ->select('nom_agence', 'id')
                       ->get();

        return response()->json([
            'chauffeur' => $chauffeur,
            'agences' => $agences
        ]);
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
        $request->validate([
            'agence_expedition' => 'required',
            'tel_chauffeur' => 'required|string|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $chauffeur = Chauffeur::findOrFail($id);

        // Correction du nom de colonne (agence_id au lieu de agence)
        $chauffeur->agence_id = $request->agence_expedition;
        $chauffeur->tel = $request->tel_chauffeur;

        // Mettre à jour le mot de passe seulement si un nouveau mot de passe est fourni
        if ($request->filled('password')) {
            $chauffeur->password = Hash::make($request->password);
        }

        $chauffeur->save();

        return response()->json(['success' => 'Chauffeur mis à jour avec succès.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $chauffeur = Chauffeur::findOrFail($id);
            $chauffeur->delete();
    
            return response()->json(['success' => 'Chauffeur supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du chauffeur.'], 500);
        }
    }
// ajax
public function get_chauffeur_list(Request $request)
{
    if ($request->ajax()) {
        $chauffeur = Chauffeur::whereHas('agence', function ($query) {
            $query->where('pays_agence', 'Côte d\'Ivoire');
        })->select(['id', 'nom', 'prenom', 'tel', 'email', 'agence_id']);

        return DataTables::of($chauffeur)
            ->addColumn('agence', function ($row) {
                return $row->agence->nom_agence;
            })
            ->addColumn('action', function ($row) {
                // Correction ici : utiliser le nom complet de la route
                $editUrl = route('IPMSANGRE_transport.chauffeurs.edit', ['id' => $row->id]);
                $deleteUrl = route('IPMSANGRE_transport.chauffeurs.destroy', ['id' => $row->id]);

                return '
                    <button class="btn btn-sm btn-primary edit-chauffeur-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#editChauffeurModal" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-chauffeur-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return abort(404);
}
// autocomplete
public function reference_auto($query)
{
    try {
        $results = Colis::with('destinataire') // Charger les informations du destinataire
            ->where('reference_colis', 'LIKE', "%$query%")
            ->whereIn('etat', ['Dechargé', 'Validé']) // Filtrer par état
            ->select('id', 'reference_colis', 'destinataire_id')
            ->get();

        return response()->json($results->map(function ($colis) {
            return [
                'label' => $colis->reference_colis,
                'value' => $colis->reference_colis,
                'reference_colis' => $colis->reference_colis,
                'destinataire_nom' => $colis->destinataire->nom,
                'destinataire_prenom' => $colis->destinataire->prenom,
                'destinataire_email' => $colis->destinataire->email,
                'destinataire_tel' => $colis->destinataire->tel,
                'destinataire_lieu' => $colis->destinataire->lieu_destination,
                'id' => $colis->id
            ];
        }));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}





    public function store_chauffeur(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'nom_chauffeur' => 'required|string|max:255',
            'prenom_chauffeur' => 'required|string|max:255',
            'email_chauffeur' => 'required|email|max:255',
            'tel_chauffeur' => 'required|string|max:255',
            // 'agence_expedition' => 'required|exists:agences,id', // Assurez-vous que l'agence existe
            'agence_expedition' => 'required|exists:agences,id', // Validation renforcée

        ]);
        try {
            Chauffeur::create([
                'nom' => $request->nom_chauffeur,
                'prenom' => $request->prenom_chauffeur,
                'email' => $request->email_chauffeur,
                'tel' => $request->tel_chauffeur,
                'agence_id' => $request->agence_expedition, // Nom de colonne cohérent

                // 'agence' => $request->agence_expedition,
                // dd($request->all())
            ]);
            return redirect()->back()->with('success', 'Chauffeur ajouté avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'ajout du chauffeur.');
        }
    }

    public function store_plannification(Request $request)
    {

            $chauffeurId = $request->input('chauffeur_id');
            $chauffeurDetails = json_decode($request->input('chauffeur_details_data'), true);
            // dd($chauffeurDetails);

            // Exemple : Traitement des données
            foreach ($chauffeurDetails as $detail) {
                // Enregistrer les données ou effectuer un traitement spécifique
                Programme::create([
                    'chauffeur_id' => $chauffeurId,
                    'colis_id' => $detail['id'],
                    'date_programme' => now(), // Exemple de date
                    'status' => 'En attente',
                ]);
            }

            return redirect()->back()->with('success', 'Plannification enregistrée avec succès !');

    }
}
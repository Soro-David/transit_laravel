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
use App\Mail\ChauffeurWelcomeMail;
use Illuminate\Support\Facades\Mail;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.transport.index');
    }
    public function show_chauffeur()
    {
        $agences = Agence::select('nom_agence', 'id')->get();
        return view('admin.transport.chauffeur', compact('agences'));
    }

    public function planing_chauffeur()
    {
        $agences = Agence::select('nom_agence', 'id')->get();
        $chauffeurs = Chauffeur::select('nom','prenom','id')->get();
        return view('admin.transport.planing', compact('agences','chauffeurs'));
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
// ajax
    public function get_chauffeur_list(Request $request)
    {
        if ($request->ajax()) {
            $chauffeur = Chauffeur::select(['id','nom', 'prenom', 'tel', 'email','agence']);
            return DataTables::of($chauffeur)
                ->addColumn('action', function ($row) {
                    $editUrl = '/users/' . $row->id . '/edit';
                    $deleteUrl = '/users/' . $row->id; // Route pour supprimer (à adapter)

                    return '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modify">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML agent.transport
                ->make(true);
        }
    }


// function pour le programme chauffeur
    public function programme_chauffeur()
    {
        // $agences = Agence::select('nom_agence', 'id')->get();
        // $chauffeurs = Chauffeur::select('nom','prenom','id')->get();
        return view('admin.transport.programme');
    }
// edit du programme chauffeur
    public function edit_programme($id)
    {
        // Récupérer tous les programmes pour le chauffeur avec l'ID spécifié
        $programmes = Programme::where('chauffeur_id', $id)->with('colis')->get();

        return view('admin.transport.edit_programme', compact('programmes'));
    }

    // function de suppression du programme 
    public function delete_chauffeur($id)
    {
        // Trouver le chauffeur
        $chauffeur = Chauffeur::find($id);

        if ($chauffeur) {
            // Supprimer tous les programmes associés
            $chauffeur->programmes()->delete();

            // // Supprimer le chauffeur
            // $chauffeur->delete();

            return response()->json(['success' => 'Le programme pour ce chauffeur a été supprimés avec succès.']);
        }

        return response()->json(['error' => 'Programme  non trouvé.'], 404);
    }
    // Ajax pour obtenir la liste des programmes
    public function get_programme_list(Request $request)
    {
        if ($request->ajax()) {
            // Récupérer les chauffeurs associés aux programmes
            $chauffeurs = Chauffeur::whereHas('programmes')
                ->select(['id', 'nom', 'prenom', 'tel', 'email'])
                ->distinct()
                ->get(); // Assurez-vous de récupérer les résultats
    
            return DataTables::of($chauffeurs)
                ->addColumn('action', function ($row) {
                    $editUrl = route('transport.programme.edit', ['id' => $row->id]);
                    $deleteUrl = route('transport.programme.delete', ['id' => $row->id]); // Route pour supprimer
    
                    return '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-url="' . $deleteUrl . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) // Permet de rendre le HTML agent.transport
                ->make(true);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401); // Gérer les requêtes non AJAX
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
        'agence_expedition' => 'required'
    ]);

    try {
        // Créer le chauffeur
        $chauffeur = Chauffeur::create([
            'nom' => $request->nom_chauffeur,
            'prenom' => $request->prenom_chauffeur,
            'email' => $request->email_chauffeur,
            'tel' => $request->tel_chauffeur,
            'agence' => $request->agence_expedition,
        ]);

        // Envoyer l'e-mail de bienvenue
        Mail::to($chauffeur->email)->send(new ChauffeurWelcomeMail($chauffeur));

        return redirect()->back()->with('success', 'Chauffeur ajouté avec succès et e-mail envoyé !');
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

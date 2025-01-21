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
                ->rawColumns(['action']) // Permet de rendre le HTML
                ->make(true);
        }
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
            'agence_expedition' => 'required' // Assurez-vous que l'agence existe
        ]);
        try {
            Chauffeur::create([
                'nom' => $request->nom_chauffeur,
                'prenom' => $request->prenom_chauffeur,
                'email' => $request->email_chauffeur,
                'tel' => $request->tel_chauffeur,
                'agence' => $request->agence_expedition,
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

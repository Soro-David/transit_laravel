<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Chauffeur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

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
        $pays_agence = Agence::distinct()->pluck('pays_agence');
        return view('admin.transport.chauffeur', compact('agences', 'pays_agence'));
    }

    public function planing_chauffeur()
    {
        $agences = Agence::select('nom_agence', 'id')->get();

        return view('admin.transport.planing', compact('agences'));
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
    public function destroyChauffeur($id)
    {
        try {
            $chauffeur = Chauffeur::findOrFail($id);
            $chauffeur->delete();
            return response()->json(['success' => 'Chauffeur supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression.'], 500);
        }
    }

    public function editChauffeur($id)
    {
        $chauffeur = Chauffeur::findOrFail($id);
        $agences = Agence::all();
        return response()->json(['chauffeur' => $chauffeur, 'agences' => $agences]);
    }

    public function updateChauffeur(Request $request, $id)
{
    $request->validate([
        'agence_expedition' => 'required|exists:agences,id',
        'password' => 'nullable|string|min:8|confirmed', // Mot de passe facultatif
        'tel_chauffeur' => 'required|string|max:255',  // Add validation rule for tel_chauffeur
    ]);

    $chauffeur = Chauffeur::findOrFail($id);

    $chauffeur->agence_id = $request->agence_expedition;
    $chauffeur->tel = $request->tel_chauffeur;  // Update the tel field

    // Si un nouveau mot de passe est fourni, le mettre à jour
    if ($request->filled('password')) {
        $chauffeur->password = Hash::make($request->password);
    }
    $agence = Agence::find($request->agence_expedition);

    $chauffeur->agence = $agence->nom_agence;

    $chauffeur->save();

    return redirect()->route('transport.show.chauffeur')->with('success', 'Chauffeur mis à jour avec succès !');
}
    // ajax
    public function get_chauffeur_list(Request $request)
{
    if ($request->ajax()) {
        $chauffeurs = Chauffeur::with('agence')  // Eager load the agency relationship
            ->select(['chauffeurs.id', 'chauffeurs.nom', 'chauffeurs.prenom', 'chauffeurs.tel', 'chauffeurs.email', 'chauffeurs.agence_id']);

        // Filter by agency country
        if ($request->has('pays_agence') && $request->get('pays_agence') != '') {
            $chauffeurs->join('agences', 'chauffeurs.agence_id', '=', 'agences.id')
                ->where('agences.pays_agence', $request->get('pays_agence'));
        }

        return DataTables::of($chauffeurs)
            ->addColumn('agence', function ($chauffeur) {
                return $chauffeur->agence ? $chauffeur->agence->nom_agence : 'N/A';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-primary edit-chauffeur-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#editChauffeurModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-chauffeur-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}

    // autocomplete
    public function reference_auto($query)
    {
        $results = User::where('email', 'LIKE', "%$query%")
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return response()->json($results);
    }


    public function store_chauffeur(Request $request)
    {
        // Valider les données du formulaire, incluant le mot de passe
        $request->validate([
            'nom_chauffeur' => 'required|string|max:255',
            'prenom_chauffeur' => 'required|string|max:255',
            'email_chauffeur' => 'required|email|max:255|unique:chauffeurs,email',
            'tel_chauffeur' => 'required|string|max:255',
            'agence_expedition' => 'required|exists:agences,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Use a transaction to ensure both user and chauffeur creation succeed or fail together.
        return DB::transaction(function () use ($request) {
            // 1. Create the user
            $user = User::create([
                'first_name' => $request->prenom_chauffeur,
                'last_name' => $request->nom_chauffeur,
                'email' => $request->email_chauffeur,
                'password' => Hash::make($request->password),
                'role' => 'chauffeur',
                'agence_id' => $request->agence_expedition,
            ]);

            // 2. Create the chauffeur record
            $agence = Agence::find($request->agence_expedition); // Récupère l'agence

            Chauffeur::create([
                'nom' => $request->nom_chauffeur,
                'prenom' => $request->prenom_chauffeur,
                'email' => $request->email_chauffeur,
                'tel' => $request->tel_chauffeur,
                'agence_id' => $request->agence_expedition, // Store the agency ID
                'agence' => $agence->nom_agence,  // Store the agency name
                'password' => Hash::make($request->password),
            ]);

            // Rediriger avec un message de succès
            return redirect()->route('transport.index')->with('success', 'Chauffeur ajouté avec succès!');
        });
    }
}
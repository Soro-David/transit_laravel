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
            $chauffeur = Agence::select(['id','nom_agence', 'adresse_agence', 'pays_agence', 'devise_agence', 'prix_au_kg']);
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
    $results = User::where('email', 'LIKE', "%$query%")
        ->select('id', 'first_name', 'last_name', 'email')
        ->get();

    return response()->json($results);
}


public function store_chauffeur(Request $request)
    {
        // Valider les données du formulaire
        $request->validate([
            'nom_chauffeur' => 'required|string|max:255',
            'email_chauffeur' => 'required|email|max:255',
            'tel_chauffeur' => 'required|string|max:255',
            // 'agence_expedition' => 'required|exists:agences,id', // Assurez-vous que l'agence existe
            'agence_id' => 'required' // Assurez-vous que l'agence existe
        ]);
    // dd($request->all());
        // Créer un nouveau chauffeur
        Chauffeur::create([
            'nom' => $request->nom_chauffeur,
            'email' => $request->email_chauffeur,
            'tel' => $request->tel_chauffeur,
            'agence_id' => $request->agence_id,
        ]);
    
        // Rediriger avec un message de succès
        return redirect()->route('chauffeur.index')->with('success', 'Chauffeur ajouté avec succès!');
    }

    
}

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

class AgenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $agences = Agence::paginate(10);
        // return view('admin.gestion.agence', compact('agences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
       

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Agence::create([
            'nom_agence' => $request->nom_agence,
            'adresse_agence' => $request->adresse_agence,
            'pays_agence' => $request->pays_agence,
            'devise_agence' => $request->devise_agence,
            'prix_au_kg' => $request->prix_au_kg,
        ]);
        return redirect()->back()->with('success', 'Agence ajouté avec succès !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    public function hold()
    {
        return view('admin.colis.hold');
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

    public function get_agence(Request $request)
    {
        if ($request->ajax()) {
            $agence = Agence::select(['id','nom_agence', 'adresse_agence', 'pays_agence', 'devise_agence', 'prix_au_kg']);
            return DataTables::of($agence)
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

public function devis_hold()
    {
        return view('admin.devis.hold');
    }

public function liste_contenaire()
    {
        return view('admin.devis.liste_contenaire');
    }

}

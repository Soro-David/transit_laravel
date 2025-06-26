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
use App\Models\Agent;

class GestionAgentController extends Controller
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

    
  
    public function edit($id)
    {
        $users = User::findOrFail($id);
        
        return view('admin.gestion.agent.edit', compact('users'));
    }

    public function show($id)
    {
        $users = User::findOrFail($id);
        // dd($users );
        return view('admin.gestion.agent.show', compact('users'));
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
        // 'nom_agence' => 'required|string|max:255',
        // 'adresse_agence' => 'required|string|max:255',
        // 'pays_agence' => 'required|string|max:255',
        // 'devise_agence' => 'required|string|max:255',
        // 'prix_au_kg' => 'required|numeric',
    ]);

    $agence = Agence::findOrFail($id);
    $agence->update($request->all());

    return redirect()->route('managers.agence')->with('success', 'Agent mise à jour avec succès!');
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
            $agence = Agence::findOrFail($id);
            $agence->delete();
            return response()->json(['success' => true, 'message' => 'Agence supprimée avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.']);
        }
    }


public function get_users(Request $request) // Renommer en get_agents serait plus cohérent
{
    if ($request->ajax()) {
        // 1. On charge la relation 'agence' avec with() pour optimiser la requête.
        // On sélectionne toutes les colonnes de la table 'agents' explicitement.
        $data = Agent::with('agence')->select('agents.*');

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                // Vos URLs de routes
                $editUrl = route('agence.agent.edit', $row->id);
                $showUrl = route('agence.agent.show', $row->id);
                $deleteUrl = route('agence.agent.destroy', $row->id);

                return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="Voir"><i class="fas fa-eye"></i></a>
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                    <button class="btn btn-sm btn-danger delete-btn" data-url="' . $deleteUrl . '"><i class="fas fa-trash"></i></button>
                ';
            })
            // 2. On ajoute une nouvelle colonne pour le nom de l'agence
            ->addColumn('agence', function ($agent) {
                // On utilise l'opérateur "nullsafe" (?->) pour éviter une erreur si un agent n'a pas d'agence
                return $agent->agence?->nom_agence ?? 'N/A';
            })
            ->rawColumns(['action']) // Indique à DataTables que la colonne 'action' contient du HTML
            ->make(true);
    }
}

    
}

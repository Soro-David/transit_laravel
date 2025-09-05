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

    public function add_agent()
    {
        $agences = Agence::select('nom_agence', 'id')->get();
        return view('admin.gestion.agent.index', compact('agences'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'role'       => 'required|string',
            'password'   => 'nullable|string|min:6|confirmed', // mot de passe facultatif
        ]);

        // Récupération de l'utilisateur
        $user = User::findOrFail($id);

        // Mise à jour User
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->role       = $request->role;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Récupération de l’agent lié
        $agent = Agent::where('user_id', $user->id)->first();

        if ($agent) {
            $agent->nom    = $request->first_name;
            $agent->prenom = $request->last_name;
            $agent->email  = $request->email;

            if (!empty($request->password)) {
                $agent->password = Hash::make($request->password);
            }

            $agent->save();
        }

        return redirect()->route('managers.agent')->with('success', 'Agent mis à jour avec succès!');
    }
  
    public function edit($id)
    {
        // dd($id);
        $users = Agent::findOrFail($id);
        
        dd($users );
        return view('admin.gestion.agent.edit', compact('users'));
    }

    public function show($id)
    {
        // dd($id);
        $users = Agent::findOrFail($id);
        dd($users );
        return view('admin.gestion.agent.show', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $agent = Agent::findOrFail($id);

            // Supprimer l'utilisateur lié si présent
            if ($agent->user) {
                $agent->user->delete();
            }

            // Supprimer l'agent
            $agent->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agent et utilisateur supprimés avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ]);
        }
    }



public function get_users(Request $request)
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

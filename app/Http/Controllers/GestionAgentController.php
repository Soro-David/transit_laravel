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


    public function get_users(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
            return DataTables::of($users)
                ->addColumn('action', function ($row) {
                    $editUrl = route('agence.agent.edit', ['id' => $row->id]);
                    $showUrl = route('agence.agent.show', ['id' => $row->id]);
                    $deleteUrl = route('agence.agent.destroy', ['id' => $row->id]);
                    return '
                        <a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="Edit" data-bs-target="#editModal">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning" title="Modify" data-bs-target="#modifModal">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) 
                ->make(true);
        }
    }

    
}

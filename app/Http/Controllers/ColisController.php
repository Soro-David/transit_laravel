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
use App\Models\Client;


class ColisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $agences = Agence::select('nom_agence', 'id')->get();

        // Selection les clients dont le type de client est expediteur
        $client_expediteurs = Client::select('nom', 'prenom')
            ->where('type_client', 'expediteur')
            ->get();

        // Selection les clients dont le type de client est destinataire
        $client_destinataires = Client::select('nom', 'prenom')
            ->where('type_client', 'destinataire')
            ->get();

        return view('admin.colis.add', compact('agences','client_expediteurs', 'client_destinataires'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = [
            'description' => $request->description,
            'quantite' => $request->quantite,
            'dimension' => $request->dimension,
            'prix' => $request->prix,
        ];
        return response()->json($product);
    }

    public function store_expediteur(Request $request)
    {
        dd($request);
        $expediteur = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'destinataire', // Par défaut 'destinataire'
        ]);
        return redirect()->back()->with('success', 'expediteur cree avec succès !');
    }

    public function store_destinataire(Request $request)
    {
        $destinataire = Client::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'lieu_livraison' => $request->lieu_livraison,
            'agence' => $request->agence,
            'type_client' => $request->type_client ?? 'expediteur', // Par défaut 'expediteur'
        ]);

        return redirect()->back()->with('success', 'Destinataire créé avec succès !');
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

    public function get_colis(Request $request)
{
    if ($request->ajax()) {
        $users = User::select(['id', 'first_name', 'email', 'role', 'created_at']);
        return DataTables::of($users)
            ->addColumn('action', function ($row) {
                $editUrl = '/users/' . $row->id . '/edit';

                return '
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-info" title="View" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-success" title="Payment" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-credit-card"></i>
                        </a>
                    </div>
                   
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

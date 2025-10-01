<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

class ClientController extends Controller
{
    public function index()
    {
        // Retourne la vue d'index des clients
        return view('admin.client.index');
    }

    public function getClientsData(Request $request)
    {
        if ($request->ajax()) {
            // Sélectionne uniquement les champs nécessaires
            $data = Client::select([
                'id',  
                'nom', 
                'prenom', 
                'telephone', 
                'email', 
                'adresse', 
                'type_client', 
                'agence', 
                'created_at'
            ]);

            // Renvoie les données sous format DataTables
            return DataTables::of($data)
                ->make(true);
        }

        // Retourne une erreur si la requête n'est pas de type AJAX
        return response()->json(['error' => 'Unauthorized'], 403);
    }


public function search(Request $request)
{
    $query = $request->get('q');

    if (!$query) {
        return response()->json([]);
    }

    $clients = User::where('role', 'user') // Ne récupérer que les users
        ->where(function($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
              ->orWhere('last_name', 'like', "%$query%")
              ->orWhere('tel', 'like', "%$query%");
        })
        ->limit(10)
        ->get();

    $results = $clients->map(function($client) {
        $category = $client->role === 'societe' ? 'societe' : 'particulier';

        return [
            'id'         => $client->id,
            'text'       => $client->first_name . ' ' . $client->last_name,
            'first_name' => $client->first_name,
            'last_name'  => $client->last_name,
            'email'      => $client->email,
            'tel'        => $client->tel,
            'adresse'    => $client->adresse ?? '',
            'category'   => $category,
        ];
    });

    return response()->json($results);
}



}

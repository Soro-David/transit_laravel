<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
                'id',            // Ajoutez l'id si vous prévoyez d'utiliser une action sur chaque ligne
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
}

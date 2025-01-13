<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Colis;

class ClientController extends Controller
{
    //
    public function index()
    {

        return view('admin.client.index');
    }


     // AJAX pour la liste des clients
     public function get_client(Request $request)
     {
         if ($request->ajax()) {
             $client = User::select(
                'first_name',
                'last_name',
                'role',
                'email',
                'created_at'
             )
             ->where('role', 'user')
             ->get(); 
             return DataTables::of($client)
                 ->make(true);
         }
     }
}

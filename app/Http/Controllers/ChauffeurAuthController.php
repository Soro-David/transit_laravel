<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChauffeurAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            // Récupérer l'utilisateur après l'authentification réussie
            $user = Auth::user();
            if ($user->role === 'chauffeur') {
                // Redirection vers le dashboard chauffeur
                return redirect()->route('chauffeur.dashboard');
            }
             Auth::logout();
             return redirect()->back()->with('error', 'Vous n\'avez pas le rôle chauffeur.');
        }

        return redirect('/login')->with('error', 'Email ou mot de passe incorrect.');
    }
    
   public function dashboard() {
        return view('chauffeur.dashboard');
    }

     public function index()
    {
        return view('chauffeur.auth.login');
    }
}
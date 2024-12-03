<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('role:user');
    }

    public function index()
    {
        // dd(request());
        return view('customer.dashboard');
    }

    public function update_profile_photo(Request $request)
    {
        // Étape 1 : Validation de l'entrée
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
        'profile_photo.required' => 'La photo de profil est obligatoire.',
        'profile_photo.image' => 'Le fichier doit être une image.',
        'profile_photo.mimes' => 'Les formats acceptés sont : jpeg, png, jpg, gif.',
        'profile_photo.max' => 'La taille maximale autorisée est de 2 Mo.',
    ]);

    // Étape 2 : Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Étape 3 : Supprimer l'ancienne photo de profil si elle existe
    if ($user->profile_photo_path) {
        if (Storage::exists($user->profile_photo_path)) {
            Storage::delete($user->profile_photo_path);
        }
    }

    // Étape 4 : Enregistrer la nouvelle photo
    $newPhotoPath = $request->file('profile_photo')->store('profile_photos');

    // Étape 5 : Mettre à jour le chemin dans la base de données
    $user->update(['profile_photo_path' => $newPhotoPath]);

    // Étape 6 : Rediriger avec un message de succès
    return redirect()->back()->with('success', 'Votre photo de profil a été mise à jour avec succès.');
    }
    
    
}

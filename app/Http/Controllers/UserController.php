<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- IMPORTANT : Ajoutez cette ligne
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Colis;
use App\Models\Expediteur;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:user');
    }

    // On injecte "Request" pour pouvoir lire les paramètres de l'URL
    public function index(Request $request)
    {
        $user = Auth::user();
        $expediteurIds = Expediteur::where('user_id', $user->id)->pluck('id');

        // Les compteurs globaux en haut de la page ne changent pas
        $devisEnAttenteCount = Colis::whereIn('expediteur_id', $expediteurIds)->where('etat', 'En attente')->distinct('reference_colis')->count();
        $colisEnCoursCount = Colis::whereIn('expediteur_id', $expediteurIds)->whereIn('etat', ['Validé', 'En entrepot', 'Chargé', 'En transit', 'Dechargé'])->distinct('reference_colis')->count();
        $colisLivresCount = Colis::whereIn('expediteur_id', $expediteurIds)->where('etat', 'Livré')->distinct('reference_colis')->count();

        // --- SECTION MODIFIÉE POUR LE FILTRE ---

        // 1. On récupère le filtre de l'URL. Par défaut, on affiche 'en_cours'.
        $filter = $request->input('filter', 'en_cours');

        // 2. On prépare la requête de base pour le suivi
        $suiviQuery = Colis::select('reference_colis', DB::raw('MAX(updated_at) as last_updated_at'))
            ->whereIn('expediteur_id', $expediteurIds)
            ->where('etat', '!=', 'En attente') // On exclut toujours les devis non validés
            ->groupBy('reference_colis');

        // 3. On applique le filtre sur la requête
        if ($filter === 'livre') {
            // Si on veut voir les colis livrés, on ne prend que ceux avec l'état 'Livré' ou 'Fermé'
            $suiviQuery->whereIn('etat', ['Livré', 'Fermé']);
        } else { // Par défaut, 'en_cours'
            // Sinon, on prend tous les colis qui NE SONT PAS 'Livré' ou 'Fermé'
            $suiviQuery->whereNotIn('etat', ['Livré', 'Fermé']);
        }

        // 4. On exécute la requête finale
        $references = $suiviQuery->orderBy('last_updated_at', 'desc')
                                ->limit(10) // On peut augmenter la limite si besoin
                                ->pluck('reference_colis');
        
        // --- FIN DE LA SECTION MODIFIÉE ---

        $colisPourSuivi = [];
        foreach ($references as $reference) {
            $latestColis = Colis::where('reference_colis', $reference)
                                ->orderBy('updated_at', 'desc')
                                ->first();
            
            if ($latestColis) {
                $colisPourSuivi[] = [
                    'reference' => $latestColis->reference_colis,
                    'etat' => $latestColis->etat,
                    'mode_transit' => $latestColis->mode_transit,
                ];
            }
        }
        
        return view('customer.dashboard', compact(
            'devisEnAttenteCount', 
            'colisEnCoursCount',
            'colisLivresCount',
            'colisPourSuivi',
            'filter' // On passe le filtre actuel à la vue pour savoir quel bouton est actif
        ));
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

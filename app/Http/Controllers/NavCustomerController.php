<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Notifications;
use App\Models\Product;

class NavCustomerController extends Controller
{
    //
    public function index()
    {

        return view('customer.layouts.partials.navbar');
    }

    public function get_notifications(Request $request)
{
    try {
        $user = Auth::user()->id;

        // Obtenir les colis validés de l'utilisateur authentifié
        $colis = Colis::where('etat', 'Validé')
                      ->where('client_id', $user)  // Assurez-vous d'ajouter la condition pour l'ID de l'utilisateur
                      ->orderBy('created_at', 'desc')
                      ->get();

        // Compter le nombre de colis validés
        $count = $colis->count();

        // Créer des notifications pour chaque colis validé
        foreach ($colis as $colisItem) {
            // Vérifier si une notification n'a pas déjà été créée pour ce colis
            $existingNotification = Notifications::where('message', "Nouveau colis ajouté : {$colisItem->description}")
                                                 ->where('user_id', $user) // Vérifiez si la notification existe pour l'utilisateur
                                                 ->first();
            
            // Si aucune notification existante n'est trouvée, en créer une
            if (!$existingNotification) {
                Notifications::create([
                    'user_id' => $user,  // Assurez-vous d'enregistrer l'ID de l'utilisateur
                    'message' => "Nouveau colis ajouté : {$colisItem->description}",
                    'created_at' => $colisItem->created_at,
                    'updated_at' => $colisItem->created_at,
                ]);
            }
        }

        // Retourner les notifications en JSON
        return response()->json([
            'success' => true,
            'count' => $count,
            'notifications' => $colis->map(function ($colis) {
                return [
                    'id' => $colis->id,
                    'message' => "Nouveau colis ajouté : {$colis->description}",
                    'time' => $colis->created_at->diffForHumans(),
                ];
            }),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue : ' . $e->getMessage(),
        ], 500);
    }
}

    public function markAsRead(Request $request)
    {
        try {
            $notificationId = $request->input('notification_id');
            $notification = Notifications::find($notificationId); // Assurez-vous de spécifier le bon modèle Notifications
    
            if ($notification) {
                // Marquer la notification comme lue
                $notification->read_at = now(); // Ajouter un champ 'read_at' dans votre modèle Notifications
                $notification->save();
    
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Notification introuvable']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de la notification']);
        }
    }
    


}

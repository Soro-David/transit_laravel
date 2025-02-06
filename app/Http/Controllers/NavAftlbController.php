<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;
use App\Models\Notifications;
use App\Models\Product;

class NavAftlbController extends Controller
{
    //
    public function index()
    {

        return view('agent.layouts.navbar');
    }

    public function get_notifications(Request $request)
    {
        try {
            // Obtenir les colis avec l'état "En attente"
            $colis = Colis::where('etat', 'En attente')->orderBy('created_at', 'desc')->get();

            // Compter le nombre de colis en attente
            $count = $colis->count();

            foreach ($colis as $colisItem) {
                // Vérifier si une notification n'a pas déjà été créée pour ce colis
                $existingNotification = Notifications::where('message', "Nouveau colis ajouté : {$colisItem->description}")->first();
                
                if (!$existingNotification) {
                    Notifications::create([
                        'message' => "Nouveau colis ajouté : {$colisItem->description}",
                        'created_at' => $colisItem->created_at,
                        'updated_at' => $colisItem->created_at,
                    ]);
                }
            }
            // Retourner les données en JSON
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

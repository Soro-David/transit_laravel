<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- ajouté
use App\Models\Colis;
use App\Models\Notifications; // vérifie le nom du modèle (Notifications vs Notification)
use App\Models\Product;
use App\Models\Devis;
use App\Models\DevisItems;
class NavCustomerController extends Controller
{
    public function index()
    {
        return view('customer.layouts.partials.navbar');
    }

    public function get_notifications(Request $request)
    {
        try {
            // Sécurité : vérifier utilisateur authentifié
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié.'
                ], 401);
            }

            // Obtenir les colis validés de l'utilisateur authentifié
            $colis = Colis::where('etat', 'Validé')
                        ->where('client_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

            // Compter le nombre de colis validés
            $count = $colis->count();

            // Créer des notifications pour chaque colis validé (uniquement si nécessaire)
            foreach ($colis as $colisItem) {
                $existingNotification = Notifications::where('message', "Nouveau colis ajouté : {$colisItem->description}")
                                                    ->where('user_id', $userId)
                                                    ->first();

                if (!$existingNotification) {
                    Notifications::create([
                        'user_id' => $userId,
                        'message' => "Nouveau colis ajouté : {$colisItem->description}",
                        // Attention : si Notifications gère created_at automatiquement, inutile de passer created_at ici
                        'created_at' => $colisItem->created_at,
                        'updated_at' => $colisItem->created_at,
                    ]);
                }
            }

            // Retourner les notifications en JSON (map correctement les éléments)
            return response()->json([
                'success' => true,
                'count' => $count,
                'notifications' => $colis->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'message' => "Nouveau colis ajouté : {$item->description}",
                        'time' => $item->created_at->diffForHumans(),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            // Log utile pour debug
            \Log::error('Erreur get_notifications: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
            $notification = Notifications::find($notificationId);

            if ($notification) {
                // Assure-toi que ta table/model Notifications a bien un champ 'read_at'
                $notification->read_at = now();
                $notification->save();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Notification introuvable'], 404);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur markAsRead: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de la notification'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expediteur;
use App\Models\Destinataire;
use App\Models\Colis;
use App\Models\User; 

use Illuminate\Support\Facades\DB;
class chineMessagingController extends Controller
{

    const AGENCE_NOM = 'Agence de Chine';

    public function index()
    {
        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        $containerReferences = Colis::where('agence', self::AGENCE_NOM)
                                    ->whereNotNull('reference_contenaire')
                                    ->distinct()
                                    ->pluck('reference_contenaire');
                                    
        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        $flightReferences = Colis::where('agence', self::AGENCE_NOM)
                                ->whereNotNull('reference_vol')
                                ->distinct()
                                ->pluck('reference_vol');

        return view('AGENCE_CHINE.client.index', compact('containerReferences', 'flightReferences'));
    }

    /**
     * Fournit les données des clients au format JSON pour la DataTable.
     * Version corrigée avec logique de blocage client et filtre par nom d'agence.
     */
    public function getClients(Request $request)
    {
        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        $expediteursQuery = Expediteur::where('agence', self::AGENCE_NOM)
            ->select(
                'expediteurs.id', 'expediteurs.nom', 'expediteurs.prenom', 'expediteurs.tel',
                DB::raw("'expediteur' as type"),
                'users.id as user_id', 'users.is_active'
            )->leftJoin('users', 'expediteurs.user_id', '=', 'users.id');

        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        $destinatairesQuery = Destinataire::where('agence', self::AGENCE_NOM)
            ->select(
                'destinataires.id', 'destinataires.nom', 'destinataires.prenom', 'destinataires.tel',
                DB::raw("'destinataire' as type"),
                'users.id as user_id', 'users.is_active'
            )->leftJoin('users', 'destinataires.user_id', '=', 'users.id');
        
        // Gestion des filtres
        $containerRef = $request->input('reference_contenaire');
        $flightRef = $request->input('reference_vol');

        if (!empty($containerRef) || !empty($flightRef)) {
            // MODIFIÉ: Condition de base pour l'agence sur la requête de colis
            $colisQuery = Colis::where('agence', self::AGENCE_NOM);
            
            if (!empty($containerRef)) {
                $colisQuery->where('reference_contenaire', $containerRef);
            }
            if (!empty($flightRef)) {
                $colisQuery->where('reference_vol', $flightRef);
            }
            $expediteurIds = $colisQuery->pluck('expediteur_id')->unique();
            $destinataireIds = $colisQuery->pluck('destinataire_id')->unique();
            
            $expediteursQuery->whereIn('expediteurs.id', $expediteurIds);
            $destinatairesQuery->whereIn('destinataires.id', $destinataireIds);
        }

        // Fusion des résultats
        $clients = $expediteursQuery->get()->merge($destinatairesQuery->get())
            ->unique(function ($item) {
                return $item->nom . '|' . $item->prenom . '|' . $item->tel;
            })->values();

        return datatables()->of($clients)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchTerm = strtolower($request->search['value']);
                    $query->collection = $query->collection->filter(function ($item) use ($searchTerm) {
                        if (!is_object($item)) {
                            return false;
                        }
                        return str_contains(strtolower($item->nom ?? ''), $searchTerm) ||
                               str_contains(strtolower($item->prenom ?? ''), $searchTerm) ||
                               str_contains(strtolower($item->tel ?? ''), $searchTerm) ||
                               str_contains(strtolower($item->type ?? ''), $searchTerm);
                    });
                }
            })
            ->addColumn('type_client', function ($row) {
                return $row->type === 'expediteur'
                    ? '<span class="badge bg-primary">Client Expéditeur</span>'
                    : '<span class="badge bg-success">Client Destinataire</span>';
            })
            ->addColumn('statut', function ($row) {
                if (!isset($row->user_id)) {
                    return '<span class="badge bg-secondary">Non-utilisateur</span>';
                }
                return $row->is_active
                    ? '<span class="badge bg-success">Actif</span>'
                    : '<span class="badge bg-danger">Bloqué</span>';
            })
            ->addColumn('action', function ($row) {
                $clientName = e($row->prenom . ' ' . $row->nom);
                
                $messageBtn = '<button type="button" class="btn btn-sm btn-info message-individual-btn" data-bs-toggle="modal" data-bs-target="#sendIndividualMessageModal" data-client-id="' . $row->id . '" data-client-type="' . $row->type . '" data-client-name="' . $clientName . '"><i class="bi bi-chat-dots"></i> Message</button>';

                $blockBtn = '';
                if (isset($row->user_id)) {
                    $btnClass = $row->is_active ? 'btn-danger' : 'btn-success';
                    $btnIcon = $row->is_active ? 'bi-lock-fill' : 'bi-unlock-fill';
                    $btnText = $row->is_active ? 'Bloquer' : 'Débloquer';
                    $blockBtn = '<button type="button" class="btn btn-sm ' . $btnClass . ' toggle-block-btn" data-user-id="' . $row->user_id . '" data-user-name="' . $clientName . '"><i class="bi ' . $btnIcon . '"></i> ' . $btnText . '</button>';
                }

                return '<div class="btn-group" role="group">' . $messageBtn . $blockBtn . '</div>';
            })
            ->rawColumns(['type_client', 'statut', 'action'])
            ->make(true);
    }
    
    /**
     * Envoie un message groupé aux clients basés sur les filtres actifs.
     */
    public function sendFilteredMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'recipient_type' => 'required|in:expediteurs,destinataires',
            'reference_contenaire' => 'nullable|string',
            'reference_vol' => 'nullable|string',
        ]);

        $message = $request->input('message');
        $recipientType = $request->input('recipient_type');
        $refConteneur = $request->input('reference_contenaire');
        $refVol = $request->input('reference_vol');

        if (empty($refConteneur) && empty($refVol)) {
            return back()->with('error', 'Veuillez sélectionner un conteneur ou un vol pour envoyer un message.');
        }

        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        $colisQuery = Colis::where('agence', self::AGENCE_NOM);
        $filterName = '';

        if (!empty($refConteneur)) {
            $colisQuery->where('reference_contenaire', $refConteneur);
            $filterName = "conteneur {$refConteneur}";
        } else {
            $colisQuery->where('reference_vol', $refVol);
            $filterName = "vol {$refVol}";
        }

        if ($recipientType === 'expediteurs') {
            $clientIds = $colisQuery->pluck('expediteur_id')->unique();
            // MODIFIÉ: Condition pour s'assurer que les clients appartiennent à la bonne agence
            $clients = Expediteur::where('agence', self::AGENCE_NOM)->whereIn('id', $clientIds)->get();
        } else {
            $clientIds = $colisQuery->pluck('destinataire_id')->unique();
            // MODIFIÉ: Condition pour s'assurer que les clients appartiennent à la bonne agence
            $clients = Destinataire::where('agence', self::AGENCE_NOM)->whereIn('id', $clientIds)->get();
        }

        // === LOGIQUE D'ENVOI SMS A IMPLEMENTER ICI ===

        return back()->with('success', "Messages envoyés avec succès aux {$recipientType} du {$filterName}.");
    }

    /**
     * Envoie un message à un client individuel.
     */
    public function sendIndividualMessage(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'client_type' => 'required|in:expediteur,destinataire',
            'message' => 'required|string|max:160'
        ]);

        $clientId = $request->input('client_id');
        $clientType = $request->input('client_type');
        $message = $request->input('message');

        // MODIFIÉ: Condition pour filtrer par le nom de l'agence
        if ($clientType === 'expediteur') {
            $client = Expediteur::where('agence', self::AGENCE_NOM)->find($clientId);
        } else {
            $client = Destinataire::where('agence', self::AGENCE_NOM)->find($clientId);
        }

        if (!$client) {
            return back()->with('error', 'Client non trouvé.');
        }

        // === LOGIQUE D'ENVOI SMS A IMPLEMENTER ICI ===

        return back()->with('success', 'Message envoyé avec succès.');
    }
}
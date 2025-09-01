<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Colis;
use App\Services\CurrencyConverterService;
use App\Models\Expediteur;
use App\Models\Destinataire;
use Carbon\Carbon;
use App\Services\InfobipSmsService;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();
        $products_count = Product::count();

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'products_count' => $products_count
        ]);
    }

    public function dashboard_customer()
    {
        // dd(request());
        return view('customer.dashboard');
    }
    
    public function dashboard_provider()
    {
        // dd(request());
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();
        $products_count = Product::count();

        return view('provider.dashboard', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'products_count' => $products_count
        ]);
        return view('provider.dashboard');
    }

            public function clients_ipms_angre(Request $request)
    {
        // Récupérer les expediteurs distincts avec leur user
        $activeUsers = Expediteur::where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche')
            ->distinct()
            ->with('user') // Assure-toi que la relation user existe dans le modèle Expediteur
            ->get(['user_id']);


        $clients = collect();

        foreach ($activeUsers as $expediteur) {
            $user = $expediteur->user; // relation User

            if (!$user) {
                continue; // au cas où un expediteur n'a pas de user lié
            }

            $clientData = [
                'id' => $user->id,
                'nom' => $user->last_name,   // à adapter selon tes colonnes
                'prenom' => $user->first_name,
                'tel' => $user->tel,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'is_active' => $user->is_active,
                'type' => '',
            ];

            $isExpediteur = $user->expediteur()->exists();
            $isDestinataire = $user->destinataire()->exists();

            if ($isExpediteur && $isDestinataire) {
                $clientData['type'] = 'expediteur et destinataire';
            } elseif ($isExpediteur) {
                $clientData['type'] = 'expediteur';
            } elseif ($isDestinataire) {
                $clientData['type'] = 'destinataire';
            } else {
                continue;
            }

            $clients->push((object) $clientData);
        }

        $uniqueClients = $clients->values();

        return view('IPMS_SIMEXCI_ANGRE.client.index', compact('uniqueClients'));
    }
    public function edit_ipms_angre($id)
    {
        $client = User::findOrFail($id);
        return view('IPMS_SIMEXCI_ANGRE.client.edit', compact('client'));
    }


    public function update_ipms_angre(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(404, 'Client non trouvé.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'required|string|max:20', // Assurez-vous du format de tel
            'email' => 'required|email|unique:users,email,' . $user->id,
            'adresse' => 'nullable|string|max:255',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'tel' => $request->tel,
            'email' => $request->email,
            'adresse' => $request->adresse,
            // Ne pas mettre à jour is_active ici, c'est pour la désactivation
        ]);

        // Si l'utilisateur est un expéditeur ou un destinataire, mettez à jour ces tables aussi
        if ($user->expediteur) {
            $user->expediteur->update([
                'nom' => $request->last_name,
                'prenom' => $request->first_name,
                'tel' => $request->tel,
                'email' => $request->email,
                // ... d'autres champs spécifiques à l'expediteur si nécessaire
            ]);
        }
        if ($user->destinataire) {
            $user->destinataire->update([
                'nom' => $request->last_name,
                'prenom' => $request->first_name,
                'tel' => $request->tel,
                'email' => $request->email,
                // ... d'autres champs spécifiques au destinataire si nécessaire
            ]);
        }

        return redirect()->route('ipms_angre_client.index')->with('success', 'Client mis à jour avec succès.');
    }


    public function deactivateAccount_ipms_angre($id)
    {
        $client = User::findOrFail($id);
        $client->update(['is_active' => false]);
        return redirect()->route('ipms_angre_client.index')->with('success', 'Compte désactivé.');
    }

    public function toggleActivation_ipms_angre($id)
    {
        $client = User::findOrFail($id);

        // Inverse l'état is_active
        $client->update(['is_active' => !$client->is_active]);

        $status = $client->is_active ? 'activé' : 'désactivé';

        return redirect()->route('ipms_angre_client.index')
                        ->with('success', "Compte {$status} avec succès.");
    }



    public function sendMessageToClient_ipms_angre(Request $request, $tel,  InfobipSmsService $InfobipSmsService)
    {
        $request->validate(['message' => 'required|string|max:160']);

        $message = $request->input('message');
        $id = $tel;

        $phoneNumber = User::where('id', $id)->value('tel');
        // dd($phoneNumber);

        if ($InfobipSmsService->sendSms($phoneNumber, $message)) {
            return back()->with('success', 'Message envoyé au client.');
        } else {
            return back()->with('error', 'Échec de l\'envoi du message.');
        }
    }

    public function sendGlobalMessage_ipms_angre(Request $request,  InfobipSmsService $InfobipSmsService)
    {
        $request->validate(['global_message' => 'required|string|max:160']);

        $message = $request->input('global_message');
        $successfulSends = 0;
        $failedSends = 0;

        // Récupérer les numéros de téléphone uniques de tous les utilisateurs actifs qui sont clients
        $phoneNumbers = User::where('is_active', true)
            ->where(function($query) {
                $query->whereHas('expediteur', function($q) {
                    $q->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                })
                ->orWhereHas('destinataire', function($q) {
                    $q->where('agence', 'IPMS-SIMEX-CI Angre 8ème Tranche');
                });
            })
            ->pluck('tel')
            ->unique()
            ->filter(function($number) {
                return preg_match('/^\+[1-9]\d{1,14}$/', $number);
            });


        foreach ($phoneNumbers as $phoneNumber) {
            if ($InfobipSmsService->sendSms($phoneNumber, $message)) {
                $successfulSends++;
            } else {
                $failedSends++;
            }
        }

        if ($successfulSends > 0) {
            $message = "Messages envoyés avec succès à {$successfulSends} clients.";
            if ($failedSends > 0) {
                $message .= " Échec pour {$failedSends} clients.";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', "Aucun message n'a pu être envoyé. Échec pour {$failedSends} clients.");
        }
    }

}

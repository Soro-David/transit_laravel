<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Devis;
use Barryvdh\DomPDF\Facade\Pdf as PDF; // ajouter en haut du fichier avec les autres use
use Illuminate\Support\Facades\Auth;

class ClientFactureController extends Controller
{
    /**
     * Affiche la liste des devis confirmés pour l'utilisateur connecté.
     */
    public function index()
    {
        $userId = Auth::id();

        // Récupère les devis de l'utilisateur avec leurs items, uniquement ceux "confirmé"
        $devisList = Devis::with('devisItems')
            ->where('user_id', $userId)
            ->where('etat', 'confirmé')
            ->orderByDesc('created_at')
            ->get();

        return view('customer.invoice.facture', compact('devisList'));
    }

    /**
     * Affiche un devis (détail) par référence (optionnel).
     */
    public function show($reference)
    {
        $userId = Auth::id();

        $devis = Devis::with('devisItems')
            ->where('reference', $reference)
            ->where('user_id', $userId)
            ->where('etat', 'confirmé')
            ->firstOrFail();

        return view('customer.invoice.show', compact('devis'));
    }

    /**
     * (Optionnel) Générer un PDF. Nécessite barryvdh/laravel-dompdf ou autre.
     */
    public function downloadPdf($reference)
    {
        $devis = Devis::with('devisItems')->where('reference', $reference)->firstOrFail();
    
        // s'assurer que la vue existe
        if (! view()->exists('customer.invoice.pdf')) {
            abort(404, 'Vue PDF introuvable');
        }
    
        $pdf = PDF::loadView('customer.invoice.pdf', compact('devis'))
                  ->setPaper('a4', 'portrait'); // optionnel
    
        return $pdf->download("facture_{$devis->reference}.pdf");
    }
}

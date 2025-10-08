<?php

namespace App\Http\Controllers\Chauffeur\Enlevement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enlevement;
use Illuminate\Support\Facades\Auth;
use App\Models\Programme;
use App\Models\ProgrammeItems;
use App\Models\Devis;
use App\Models\DevisItems;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
class EnlevementController extends Controller
{
    public function enlevement()
    {
        $chauffeur = Auth::user();
        $programmes = Programme::where('actions_a_faire', 'recuperation')
                    ->where('user_id', $chauffeur->id)
                    ->where('etat_rdv', '!=', 'effectué')
                    ->get();
        return view('chauffeur.enlevement.take', compact('programmes'));
    }

    public function depot()
    {
        $chauffeur = Auth::user();
        $programmes = Programme::where('actions_a_faire', 'depot')
                    ->where('user_id', $chauffeur->id)
                    ->where('etat_rdv', '!=', 'effectué')
                    ->get();
        return view('chauffeur.enlevement.depot', compact('programmes'));
    }
    /**
     * Génère et affiche le QR code pour un programme spécifique.
     *
     * @param Programme $programme
     * @return \Illuminate\Http\JsonResponse
     */
    public function showQrCode(Programme $programme)
    {
        // Vérifier que le chauffeur a bien accès à ce programme
        if ($programme->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        // Récupérer la quantité depuis le devis associé au programme
        // Assurez-vous que la relation existe dans votre modèle Programme.
        // Par exemple, si la référence du colis lie le programme au devis.
        $devis = Devis::where('reference', $programme->reference_colis)->first();

        // Quantité par défaut à 1 si aucun devis n'est trouvé
        $quantite = $devis ? $devis->quantite : 1;
        
        // Données à encoder dans le QR Code (exemple : référence du colis)
        $qrData = $programme->reference_colis;

        // Génération du QR Code en SVG (plus léger et scalable)
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($qrData);

        return response()->json([
            'qr_code' => $qrCode->toHtml(),
            'quantite' => $quantite,
            'reference' => $programme->reference_colis,
        ]);
    }
    public function genererEtiquette(Programme $programme)
    {
        if ($programme->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        // CORRECTION: Utiliser DevisItems au lieu de DevisItem
        $devisItems = ProgrammeItems::where('programme_id', $programme->id)->get();
        
        // Calculer la quantité totale depuis les devisItems
        $quantiteTotale = $devisItems->sum('quantite_colis');
        
        // Utiliser reference_generee comme référence principale
        $reference = $programme->reference_generee ?? 'PROG-' . $programme->id;
        
        // Générer le QR Code
        $qrCodeSvg = QrCode::size(250)->generate($reference);

        return response()->json([
            'qr_code_html' => (string) $qrCodeSvg,
            'quantite' => $quantiteTotale,
            'reference' => $reference,
        ]);
    }
    public function printEtiquette(Programme $programme)
    {
        if ($programme->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        return $this->generateEtiquetteData($programme, 'view');
    }
    public function downloadEtiquettePdf(Programme $programme)
    {
        if ($programme->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }
    
        // Vérifier l'environnement (debug)
        $this->checkPdfEnvironment();
    
        // Générer le PDF
        $data = $this->generateEtiquetteData($programme, 'pdf');
    
        try {
            $pdf = PDF::loadView('chauffeur.enlevement.print', $data);
            
            // Configuration optimisée pour GD
            $pdf->setOption('enable_php', false);
            $pdf->setOption('enable_remote', true);
            $pdf->setOption('chroot', realpath(''));
            $pdf->setOption('dpi', 150);
            $pdf->setPaper([0, 0, 226.77, 566.93], 'portrait');
    
            // Mettre à jour le statut du programme
            $programme->etat_rdv = 'effectué';
            $programme->save();
    
            $fileName = 'etiquette-' . $data['referenceAffichee'] . '.pdf';
            return $pdf->download($fileName);
            
        } catch (\Exception $e) {
            \Log::error('Erreur génération PDF: ' . $e->getMessage());
            
            // Fallback: retourner la vue HTML
            return view('chauffeur.enlevement.print', $data)
                ->with('error', 'Erreur PDF: ' . $e->getMessage());
        }
    }
    private function checkPdfEnvironment()
    {
        $checks = [
            'GD installed' => extension_loaded('gd'),
            'Imagick installed' => extension_loaded('imagick'),
            'FreeType support' => function_exists('imagettftext'),
        ];
        
        \Log::info('PDF Environment Check:', $checks);
        
        return $checks;
    }

    private function generateEtiquetteData(Programme $programme, $type = 'view')
    {
        $chauffeur = User::find($programme->user_id);
        $chauffeurName = $chauffeur ? $chauffeur->first_name . ' ' . $chauffeur->last_name : 'Chauffeur non assigné';
        
        $referenceAffichee = $programme->reference_generee ?? 'PROG-' . $programme->id;

        $etiquettes = [];

        $devisItems = ProgrammeItems::where('programme_id', $programme->id)->get();

        if ($devisItems->isNotEmpty()) {
            $compteurGlobal = 0;
            // NOUVEAU: Pré-calculer la quantité totale pour l'utiliser dans la boucle
            $quantiteTotale = $devisItems->sum('quantite_colis');
            
            foreach ($devisItems as $item) {
                for ($i = 0; $i < $item->quantite_colis; $i++) {
                    $compteurGlobal++;

                    // --- MODIFICATION PRINCIPALE ICI ---
                    // Ancien code:
                    // $qrContent = $referenceAffichee;

                    // NOUVEAU: Construire une chaîne de caractères formatée pour le QR Code.
                    // Le "\n" permet de faire un retour à la ligne pour une meilleure lisibilité lors du scan.
                    $qrContent = "Référence: " . $referenceAffichee . "\n"
                               . "Client: " . $programme->nom_expediteur . "\n"
                               . "Téléphone: " . $programme->tel_expediteur . "\n"
                               . "Adresse: " . $programme->lieu_expedition . "\n"
                               . "Nature: " . $item->type_colis . "\n"
                               . "Poids: " . $item->poids . " kg\n"
                               . "Date: " . date('d/m/Y', strtotime($programme->date_programme)) . "\n"
                               . "Opération: " . strtoupper($programme->actions_a_faire) . "\n"
                               . "Colis: " . $compteurGlobal . "/" . $quantiteTotale;
                    // --- FIN DE LA MODIFICATION ---

                    // Générer le QR code selon le type
                    if ($type === 'pdf') {
                        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(150)->generate($qrContent));
                    } else {
                        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->generate($qrContent));
                    }
                    
                    $etiquettes[] = [
                        'qrCodeBase64' => $qrCodeBase64,
                        'reference' => $referenceAffichee,
                        'service' => $item->service,
                        'type_colis' => $item->type_colis,
                        'poids' => $item->poids,
                        'description' => $item->description_colis,
                        'compteur' => $compteurGlobal,
                        'nature_colis' => $item->type_colis,
                        'date_programme' => $programme->date_programme,
                        'nom_expediteur' => $programme->nom_expediteur,
                        'tel_expediteur' => $programme->tel_expediteur,
                        'lieu_expedition' => $programme->lieu_expedition,
                        'actions_a_faire' => $programme->actions_a_faire,
                        'chauffeurName' => $chauffeurName,
                    ];
                }
            }
        } else {
            // Fallback si aucun devisItem n'est trouvé
            $quantite = $programme->quantite ?? 1;
            for ($i = 1; $i <= $quantite; $i++) {

                // --- MODIFICATION AUSSI DANS LE FALLBACK ---
                $qrContent = "Référence: " . $referenceAffichee . "\n"
                           . "Client: " . $programme->nom_expediteur . "\n"
                           . "Téléphone: " . $programme->tel_expediteur . "\n"
                           . "Adresse: " . $programme->lieu_expedition . "\n"
                           . "Nature: " . ($programme->nature_du_colis ?? 'Colis Standard') . "\n"
                           . "Poids: N/A\n"
                           . "Date: " . date('d/m/Y', strtotime($programme->date_programme)) . "\n"
                           . "Opération: " . strtoupper($programme->actions_a_faire) . "\n"
                           . "Colis: " . $i . "/" . $quantite;
                // --- FIN DE LA MODIFICATION ---
                
                if ($type === 'pdf') {
                    // J'ai aussi corrigé ici pour utiliser 'svg' comme vous l'aviez fait plus haut
                    $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(150)->generate($qrContent));
                } else {
                    $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->generate($qrContent));
                }
                
                $etiquettes[] = [
                    'qrCodeBase64' => $qrCodeBase64,
                    'reference' => $referenceAffichee,
                    'service' => $programme->nature_du_colis ?? 'Colis Standard',
                    'type_colis' => $programme->nature_du_colis ?? 'Colis Standard',
                    'poids' => 'N/A',
                    'description' => 'Colis programme',
                    'compteur' => $i,
                    'nature_colis' => $programme->nature_du_colis ?? 'Colis Standard',
                    'date_programme' => $programme->date_programme,
                    'nom_expediteur' => $programme->nom_expediteur,
                    'tel_expediteur' => $programme->tel_expediteur,
                    'lieu_expedition' => $programme->lieu_expedition,
                    'actions_a_faire' => $programme->actions_a_faire,
                    'chauffeurName' => $chauffeurName,
                ];
            }
        }

        return [
            'programme' => $programme,
            'etiquettes' => $etiquettes,
            'quantiteTotale' => count($etiquettes),
            'chauffeurName' => $chauffeurName,
            'referenceAffichee' => $referenceAffichee,
        ];
    }


    public function historique()
    {
        $chauffeur = Auth::user();
        $programmes = Programme::where('user_id', $chauffeur->id)
                    ->where('etat_rdv', 'effectué')
                    ->orderBy('updated_at', 'desc')
                    ->get();
        
        return view('chauffeur.enlevement.historique', compact('programmes'));
    }
}


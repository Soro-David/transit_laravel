<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\File;

class QrcodeController extends Controller
{
    public function generate()
    {
        // Générer le QR Code

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([]) // Options d'écriture (vide ici)
            ->data('https://www.qrcode-monkey.com') // Données pour le QR Code
            ->encoding(new Encoding('UTF-8')) // Encodage
            // ->errorCorrectionLevel(new ErrorCorrectionLevelLow()) // Niveau de correction d'erreur
            ->size(300) // Taille du QR Code
            ->margin(10) // Marge
            // ->roundBlockSizeMode(new RoundBlockSizeModeMargin()) // Mode d'arrondi des blocs
            ->labelText('Scan the code') // Texte sous le QR Code
            ->labelFont(new OpenSans(20)) // Police du label
            // ->labelAlignment(new LabelAlignment\LabelAlignmentCenter()) // Alignement du label
            ->build();

        // Définir le chemin du fichier QR code
        $filePath = 'qrcodes/qrcode.png';
        $fullPath = storage_path('app/public/' . $filePath);

        // Vérifier et créer le répertoire cible si nécessaire
        $directory = dirname($fullPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Sauvegarder le fichier QR code dans le storage
        file_put_contents($fullPath, $result->getString());
dd($result);
        // Retourner la vue avec le chemin du QR code
        return view('qrcode', compact('filePath'));
    }
}

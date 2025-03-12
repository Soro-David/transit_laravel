<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignatureController extends Controller
{
   

    public function saveSignature(Request $request)
    {
        dd($request->all());
        // Enregistre la signature (tu peux la sauvegarder dans une base de données ou un fichier)
        $signature = $request->input('signature');
        
        // Sauvegarde la signature, par exemple dans un fichier
        file_put_contents(storage_path('app/signatures/' . time() . '.png'), base64_decode($signature));
        
        return response()->json(['message' => 'Signature saved successfully']);
    }
}

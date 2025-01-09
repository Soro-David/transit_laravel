<?php
namespace App\Http\Controllers;
use App\Models\Chauffeur;
use App\Models\Programme;
use Illuminate\Http\Request;
use App\Models\Colis;
class ProgrammeController extends Controller
{
    public function index()
    {
         return view('admin.programme.programme');
    }

    public function data()
    {
         $chauffeurs = Chauffeur::all();
        $programmes = Programme::with('chauffeur')->get();
        $colisValides = colis::where('etat', 'Validé')->with('expediteur', 'destinataire')->get();
        return response()->json(['chauffeurs' => $chauffeurs, 'programmes' => $programmes, 'colisValides' => $colisValides]);
    }

    public function storeProgramme(Request $request)
    {
        \Log::info("Debut de la creation du programme");
        try {
           $rules = [
                'date_programme' => 'required|date',
                'chauffeur_id' => 'required|exists:chauffeurs,id',
            ];
             if($request->reference_colis){
                 $rules['reference_colis'] = 'exists:colis,reference_colis';
             }
             $request->validate($rules);
            //recupere les informations du colis à partir de la référence
           $colis = colis::where('reference_colis', $request->reference_colis)->with('expediteur', 'destinataire')->first();

            $programmeData = $request->all();

            if($colis){
               $programmeData['nom_expediteur'] = $colis->expediteur->nom;
                $programmeData['nom_destinataire'] = $colis->destinataire->nom;
               $programmeData['lieu_destinataire'] = $colis->destinataire->lieu_destination;
            }

            $programme = Programme::create($programmeData);
            \Log::info("programme créé avec l'id :".$programme->id);
            return redirect()->back()->with('success', 'Programme créé avec succès!');

        } catch (\Exception $e){
             \Log::error("Erreur lors de la création du programme :" .$e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création du programme : '.$e->getMessage());
        }
    }
}
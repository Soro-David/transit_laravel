<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Expediteur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProspectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index()
{
    // Récupérer tous les expéditeurs qui ne sont pas des utilisateurs
    $expediteursNonUtilisateurs = Expediteur::whereDoesntHave('user')->get();

    // Récupérer tous les prospects
    $prospectsDansTable = Prospect::all();

    // Emails des expéditeurs déjà présents (pour éviter les doublons lors de la fusion)
    $expediteurEmails = $expediteursNonUtilisateurs->pluck('email')->toArray();

    // Récupérer les prospects qui ne sont pas déjà dans les expéditeurs non utilisateurs
    $prospectsAbsentsExpediteurs = $prospectsDansTable->filter(function ($prospect) use ($expediteurEmails) {
        return !in_array($prospect->email, $expediteurEmails);
    });

    // Fusionner les deux collections de prospects
    $allProspects = $expediteursNonUtilisateurs->map(function ($expediteur) {
        return (object) [
            'id' => 'E' . $expediteur->id, // Ajout d'un préfixe pour les différencier
            'nom' => $expediteur->nom,
            'prenom' => $expediteur->prenom,
            'full_name' => trim($expediteur->nom . ' ' . $expediteur->prenom),
            'email' => $expediteur->email,
            'tel' => $expediteur->tel,
            'adresse' => $expediteur->adresse,
            'type' => 'expediteur_non_user',
            'created_at' => $expediteur->created_at
        ];
    })->concat($prospectsAbsentsExpediteurs->map(function ($prospect) {
        return (object) [
            'id' => 'P' . $prospect->id,
            'nom' => $prospect->nom,
            'prenom' => $prospect->prenom,
            'full_name' => trim($prospect->nom . ' ' . $prospect->prenom),
            'email' => $prospect->email,
            'tel' => $prospect->tel,
            'adresse' => $prospect->adresse,
            'type' => 'prospect_table',
            'created_at' => $prospect->created_at
        ];
    }));

    // Trier par date de création (les plus récents en premier)
    $allProspects = $allProspects->sortByDesc('created_at')->values();

    return view('admin.prospects.index', compact('allProspects'));
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.prospects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'nullable|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('prospects', 'email'),
            Rule::unique('users', 'email'),
            Rule::unique('expediteurs', 'email'),
        ],
        'tel' => 'nullable|string|max:20',
        'adresse' => 'nullable|string|max:255',
    ]);

    Prospect::create($validated);

    return redirect()
        ->route('prospects.index')
        ->with('success', '✅ Prospect créé avec succès.');
}


    /**
     * Display the specified resource.
     *
     * @param  string  $id (peut être 'exp_X' ou 'pro_X')
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type = explode('_', $id)[0];
        $originalId = explode('_', $id)[1];

        if ($type === 'exp') {
            $prospect = Expediteur::whereDoesntHave('user')->findOrFail($originalId);
            $prospect->type = 'expediteur_non_user';
        } elseif ($type === 'pro') {
            $prospect = Prospect::findOrFail($originalId);
            $prospect->type = 'prospect_table';
        } else {
            abort(404);
        }

        return view('admin.prospects.show', compact('prospect'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id (peut être 'exp_X' ou 'pro_X')
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = explode('_', $id)[0];
        $originalId = explode('_', $id)[1];

        if ($type === 'exp') {
            $prospect = Expediteur::whereDoesntHave('user')->findOrFail($originalId);
            $prospect->type = 'expediteur_non_user';
        } elseif ($type === 'pro') {
            $prospect = Prospect::findOrFail($originalId);
            $prospect->type = 'prospect_table';
        } else {
            abort(404);
        }

        return view('admin.prospects.edit', compact('prospect'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id (peut être 'exp_X' ou 'pro_X')
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type = explode('_', $id)[0];
        $originalId = explode('_', $id)[1];

        if ($type === 'exp') {
            $prospect = Expediteur::whereDoesntHave('user')->findOrFail($originalId);
            $rules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('expediteurs', 'email')->ignore($originalId), // Ignore l'ID actuel
                    Rule::unique('users', 'email'),
                ],
                'tel' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:255',
            ];
            $prospect->update($request->validate($rules));
        } elseif ($type === 'pro') {
            $prospect = Prospect::findOrFail($originalId);
            $rules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('prospects', 'email')->ignore($originalId), // Ignore l'ID actuel
                    Rule::unique('users', 'email'),
                    Rule::unique('expediteurs', 'email'),
                ],
                'tel' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:255',
            ];
            $prospect->update($request->validate($rules));
        } else {
            abort(404);
        }

        return redirect()->route('prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id (peut être 'exp_X' ou 'pro_X')
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = explode('_', $id)[0];
        $originalId = explode('_', $id)[1];

        if ($type === 'exp') {

            return back()->with('error', 'Impossible de supprimer un expéditeur qui est aussi un prospect via cette interface. Veuillez gérer cela manuellement si nécessaire.');
        } elseif ($type === 'pro') {
            $prospect = Prospect::findOrFail($originalId);
            $prospect->delete();
            return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
        } else {
            abort(404);
        }
    }
}
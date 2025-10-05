<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Expediteur;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ChineProspectController extends Controller
{
    public function index()
    {
        return view('AGENCE_CHINE.prospects.index');
    }

    public function getProspects(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Accès non autorisé');
        }

        // Emails à exclure
        $excludedEmails = array_merge(
            Expediteur::pluck('email')->toArray(),
            User::pluck('email')->toArray()
        );

        // Récupération prospects 
        $prospects = Prospect::whereNotIn('email', $excludedEmails)
            ->get(['nom', 'prenom', 'email', 'tel', 'created_at'])
            ->map(function ($item) {
                return (object) [
                    'full_name'  => trim($item->nom . ' ' . ($item->prenom ?? '')),
                    'email'      => $item->email,
                    'numero'     => $item->tel,
                    'type'       => 'prospect',
                    'created_at' => $item->created_at,
                ];
            });

        // Récupération contacts
        $contacts = Contact::whereNotIn('email', $excludedEmails)
            ->get(['nom', 'email', 'contact', 'created_at'])
            ->map(function ($item) {
                return (object) [
                    'full_name'  => $item->nom,
                    'email'      => $item->email,
                    'numero'     => $item->contact,
                    'type'       => 'contact',
                    'created_at' => $item->created_at,
                ];
            });

        // Fusion et unique par email, trié par date de création décroissante
        $merged = $prospects->concat($contacts);

        $unique = $merged
            ->sortByDesc('created_at')
            ->unique('email')
            ->values()
            ->map(function ($item, $index) {
                $item->id = $index + 1;
                return $item;
            });

        return DataTables::of($unique)
            ->editColumn('type', function ($row) {
                return $row->type === 'contact'
                    ? '<span class="badge bg-info">🧾 Contact</span>'
                    : '<span class="badge bg-secondary">💼 Prospect</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at
                    ? Carbon::parse($row->created_at)->format('d/m/Y H:i')
                    : '-';
            })
            ->rawColumns(['type'])
            ->make(true);
    }


    /**
     * Enregistre un nouveau prospect dans la base de données.
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
                Rule::unique('contacts', 'email'),
            ],
            'tel' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        Prospect::create($validated);

        return redirect()
            ->route('chine_prospects.index')
            ->with('success', '✅ Prospect créé avec succès.');
    }



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

        return view('AGENCE_CHINE.prospects.show', compact('prospect'));
    }


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

        return view('AGENCE_CHINE.prospects.edit', compact('prospect'));
    }


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
                    Rule::unique('expediteurs', 'email')->ignore($originalId),
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
                    Rule::unique('prospects', 'email')->ignore($originalId),
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

        return redirect()->route('aft_prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }


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
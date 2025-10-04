<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Devisrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'mode_transit' => 'required|string|in:maritime,aerien',
            'pays_expedition' => 'required|string|max:100',
            'agence_expedition' => 'required|string|max:255',
            'agence_destination_societe' => 'required|string|max:255', 
            'nom_expediteur' => 'required|string|max:255',
            'prenom_expediteur' => 'required|string|max:255',
            'email_expediteur' => 'nullable|email|max:255',
            'tel_expediteur' => 'nullable|string|max:50',
            'adresse_expediteur' => 'required|string',
            'devise' => 'required|string|in:EUR,FCFA', // Changé ici

            // Validation pour chaque colis dans le tableau
            'quantite_colis'   => 'required|array|min:1',
            'quantite_colis.*' => 'required|integer|min:1',
            'service'          => 'required|array|min:1',
            'service.*'        => 'required|string|max:255',
            'valeur_colis'     => 'nullable|array',
            'valeur_colis.*'   => 'nullable|numeric|min:0',
            'type_colis'       => 'required|array|min:1',
            'type_colis.*'     => 'required|string|in:standard,fragile', // Changé ici
            'description_colis'=> 'nullable|array',
            'description_colis.*'=> 'nullable|string',

            // Champs conditionnels
            'poids'      => 'nullable|array',
            'poids.*'    => 'nullable|numeric|min:0',
            'longueur'   => 'nullable|array',
            'longueur.*' => 'nullable|numeric|min:0',
            'largeur'    => 'nullable|array',
            'largeur.*'  => 'nullable|numeric|min:0',
            'hauteur'    => 'nullable|array',
            'hauteur.*'  => 'nullable|numeric|min:0',
            ];
    }

    public function messages(){
        return
        [
            'devise.required' => 'Le champ devise est obligatoire.',
            'type_colis.required' => 'Le type de colis est obligatoire pour chaque article.',
            'type_colis.*.required' => 'Le type de colis est obligatoire pour chaque article.',
            'quantite_colis.min' => 'Au moins un colis doit être ajouté.',
            'service.min' => 'Au moins un colis doit être ajouté.',
        ];
    }
}

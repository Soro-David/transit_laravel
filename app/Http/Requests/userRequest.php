<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class userRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
           'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'role' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'nom.required'=>'Le nom est Obligatoire',
            'prenom.required'=>'Le prenom est Obligatoire',
            'email.required'=>'Le mail est Obligatoire',
            'email.unique'=>'Cette adresse mail existe déjà',
            'password.required'=>'Le nom est Obligatoire',
            'role.required'=>'Le role est Obligatoire',
        ];
    }
}

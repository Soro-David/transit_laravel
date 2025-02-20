<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the user
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'agence_id' => $data['agence_id'],
            ]);

            // 2. Create the customer record (si nécessaire)
            Client::create([
                'nom' => $data['first_name'],
                'prenom' => $data['last_name'],
                'email' => $data['email'],
                'user_id' => $user->id,
                'telephone' => $data['tel'],
                'adresse' => $data['adresse'],
                'agence' => Agence::find($data['agence_id'])->nom_agence,
            ]);

            // 3. Create the expediteur record
            Expediteur::create([
                'nom' => $data['first_name'], // Ré-ajout du nom
                'prenom' => $data['last_name'],
                'email' => $data['email'],
                'tel' => $data['tel'],
                'adresse' => $data['adresse'],
                'agence' => Agence::find($data['agence_id'])->nom_agence,
            ]);

            return $user;
        });
    }

    public function edit($id)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        
        return view('auth.edit_register', compact('user'));
    }
}

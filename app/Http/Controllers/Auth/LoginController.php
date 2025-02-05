<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect users based on their roles after login.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('home');
        } elseif ($user->role === 'user') {
            return redirect()->route('customer.dashboard');
        } elseif ($user->role === 'agent') {
            // Vérifier si l'agent appartient à l'agence "AFT Agence Louis Bleriot"
            if (isset($user->agence) && $user->agence->nom_agence === 'AFT Agence Louis Bleriot') {
                return redirect()->route('AFT_LOUIS_BLERIOT.dashboard');
            }
            if (isset($user->agence) && $user->agence->nom_agence === 'IPMS-SIMEX-CI') {
                return redirect()->route('IPMS_SIMEXCI.dashboard');
            }
            if (isset($user->agence) && $user->agence->nom_agence === 'IPMS-SIMEX-CI Angre 8ème Tranche') {
                return redirect()->route('IPMS_SIMEXCI_ANGRE.dashboard');
            }
            if (isset($user->agence) && $user->agence->nom_agence === 'Agence de Chine') {
                return redirect()->route('AGENCE_CHINE.dashboard');
            }
            // Sinon, rediriger vers la route générale de l'agent
            return redirect()->route('agent.dashboard');
        }

        // Redirection par défaut
        return redirect('/home');
    }
}

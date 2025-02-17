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
     * Où rediriger les utilisateurs après la connexion.
     * 
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Crée une nouvelle instance du contrôleur.
     * 
     * @return void
     */
    public function __construct()
    {
        // On autorise l'accès à la page de login même pour un utilisateur déjà connecté
        // afin de permettre la déconnexion automatique.
        $this->middleware('guest')->except('logout');
    }

    /**
     * Affiche le formulaire de connexion.
     * 
     * Si un utilisateur est déjà connecté, on le déconnecte afin de lui permettre de se reconnecter avec le bon compte.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        return view('auth.login');
    }

    /**
     * Redirige les utilisateurs après leur authentification selon leur rôle.
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
            if (isset($user->agence)) {
                switch ($user->agence->nom_agence) {
                    case 'AFT Agence Louis Bleriot':
                        return redirect()->route('AFT_LOUIS_BLERIOT.dashboard');
                    case 'IPMS-SIMEX-CI':
                        return redirect()->route('IPMS_SIMEXCI.dashboard');
                    case 'IPMS-SIMEX-CI Angre 8ème Tranche':
                        return redirect()->route('IPMS_SIMEXCI_ANGRE.dashboard');
                    case 'Agence de Chine':
                        return redirect()->route('AGENCE_CHINE.dashboard');
                    default:
                        return redirect()->route('agent.dashboard');
                }
            }
            return redirect()->route('agent.dashboard');
        }
        elseif($user->role === 'chauffeur'){
            return redirect()->route('chauffeur.dashboard');
       }

        return redirect('/home');
    }
}

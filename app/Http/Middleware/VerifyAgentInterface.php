<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyAgentInterface
{
    /**
     * Vérifie que l'utilisateur authentifié correspond bien au rôle passé en paramètre
     * et qu'en cas d'agent, l'interface (la route) accédée correspond à son agence.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Le rôle attendu (ici "agent")
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Vérifier que l'utilisateur est connecté et possède le rôle requis
        if (!Auth::check() || Auth::user()->role !== $role) {
            Auth::logout();
            return redirect('/login')->with('message', 'Veuillez vous reconnecter avec le bon compte.');
        }

        // Pour un agent, vérifier que l'interface accessible correspond à son agence
        if ($role === 'agent') {
            $user = Auth::user();
            $currentRouteName = $request->route()->getName();

            // Définir les correspondances entre le nom de l'agence et le préfixe de route autorisé
            $agencyRouteMap = [
                'AFT Agence Louis Bleriot'       => 'AFT_LOUIS_BLERIOT.',
                'IPMS-SIMEX-CI'                  => 'IPMS_SIMEXCI.',
                'IPMS-SIMEX-CI Angre 8ème Tranche' => 'IPMS_SIMEXCI_ANGRE.',
                'Agence de Chine'                => 'AGENCE_CHINE.',
            ];

            $agentAgency = $user->agence->nom_agence ?? null;
            $allowedRoutePrefix = $agencyRouteMap[$agentAgency] ?? null;

            // Définir d'autres routes autorisées (facultatives)
            $additionalAllowedRoutes = [
                'route_speciale.', // par exemple
            ];

            // Fusionner le préfixe autorisé (s'il existe) avec les routes additionnelles
            $allowedRoutes = $allowedRoutePrefix
                ? array_merge([$allowedRoutePrefix], $additionalAllowedRoutes)
                : $additionalAllowedRoutes;

            $authorized = false;
            foreach ($allowedRoutes as $prefix) {
                if (str_starts_with($currentRouteName, $prefix)) {
                    $authorized = true;
                    break;
                }
            }
            if (!$authorized) {
                Auth::logout();
                return redirect('/login')->with('message', 'Vous avez été déconnecté car vous avez tenté d\'accéder à une interface non autorisée.');
            }
        }

        return $next($request);
    }
}

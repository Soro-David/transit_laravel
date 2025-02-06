<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Gère la requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role Le rôle attendu pour accéder à l’interface.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Veuillez vous connecter.');
        }

        // Si l'utilisateur est connecté mais son rôle ne correspond pas à l'interface demandée
        if (Auth::user()->role !== $role) {
            // Déconnexion de l'utilisateur
            Auth::logout();
            // Redirection vers la page de connexion avec un message d'erreur
            return redirect('/login')->with('message', 'Veuillez vous reconnecter avec le bon compte.');
        }

        return $next($request);
    }
}

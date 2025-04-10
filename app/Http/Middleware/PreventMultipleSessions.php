<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PreventMultipleSessions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifie si la session actuelle est différente de celle stockée
            if ($user->last_session_id && $user->last_session_id !== Session::getId()) {
                Auth::logout();

                return redirect('/login')->with('message', 'Vous avez été déconnecté car votre compte a été utilisé ailleurs.');
            }
        }

        return $next($request);
    }
}

<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (!function_exists('isActiveRoute')) {
    /**
     * Vérifie si la route actuelle correspond à un nom de route donné ou à un motif.
     *
     * @param string|array $routeName Le nom de la route ou un tableau de noms de routes.
     * @param string $output La classe CSS à retourner si la route est active (par défaut 'active').
     * @return string
     */
    function isActiveRoute($routeName, $output = 'active')
    {
        $current = Route::currentRouteName();
        $routes = is_array($routeName) ? $routeName : [$routeName];

        foreach ($routes as $rn) {
            if ($current === $rn || Str::is($rn, $current)) {
                return $output;
            }
        }

        return '';
    }
}

if (!function_exists('isMenuOpen')) {
    /**
     * Vérifie si l'une des routes d'un tableau correspond à la route actuelle.
     *
     * @param array $routeNames Tableau de noms de routes ou de motifs.
     * @param string $output La classe CSS à retourner si une route est active (par défaut 'menu-open').
     * @return string
     */
    function isMenuOpen(array $routeNames, $output = 'menu-open')
    {
        return isActiveRoute($routeNames, $output);
    }
}

if (!function_exists('activeSegment')) {
    /**
     * Vérifie si l'URL actuelle contient un segment donné.
     * Exemple : activeSegment('users', 2) => vérifie le segment 2 (ex: /admin/users)
     *
     * @param string $name Le nom du segment à vérifier.
     * @param int $segment Le numéro du segment dans l'URL (1 pour le premier).
     * @param string $class Classe CSS à retourner en cas de correspondance.
     * @return string
     */
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

if (!function_exists('activeTreeview')) {
    /**
     * Vérifie si l'URL actuelle contient un ou plusieurs segments donnés.
     * Utile pour activer un menu déroulant parent dans la sidebar.
     *
     * @param string|array $segments Segments à vérifier (ex: 'users', ['products', 'orders']).
     * @param string $output Classe CSS à retourner (par défaut 'menu-open').
     * @return string
     */
    function activeTreeview($segments, $output = 'menu-open') {
        $segments = is_array($segments) ? $segments : [$segments];

        foreach ($segments as $segment) {
            if (empty($segment)) {
                if (Request::is('/')) {
                    return $output;
                }
            } elseif (Request::is($segment) || Request::is($segment . '/*')) {
                return $output;
            }
        }

        return '';
    }
}

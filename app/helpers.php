<?php

if (!function_exists('isActiveRoute')) {
    /**
     * Vérifie si la route actuelle correspond à un nom de route donné ou à un motif.
     * Peut aussi vérifier si l'un des noms de route d'un tableau correspond.
     *
     * @param string|array $routeName Le nom de la route ou un tableau de noms de routes.
     * @param string $output La classe CSS à retourner si la route est active (par défaut 'active').
     * @return string
     */
    function isActiveRoute($routeName, $output = 'active')
    {
        if (is_array($routeName)) {
            foreach ($routeName as $rn) {
                if (Route::currentRouteName() == $rn) {
                    return $output;
                }
                if (Str::is($rn, Route::currentRouteName())) {
                    return $output;
                }
            }
        } else {
            if (Route::currentRouteName() == $routeName) {
                return $output;
            }
            if (Str::is($routeName, Route::currentRouteName())) {
                return $output;
            }
        }
        return '';
    }
}

if (!function_exists('isMenuOpen')) {
    /**
     * Vérifie si l'une des routes d'un tableau correspond à la route actuelle,
     * utile pour ouvrir les menus déroulants.
     *
     * @param array $routeNames Tableau de noms de routes ou de motifs.
     * @param string $output La classe CSS à retourner si une route est active (par défaut 'menu-open').
     * @return string
     */
    function isMenuOpen(array $routeNames, $output = 'menu-open')
    {
        foreach ($routeNames as $rn) {
            if (Route::currentRouteName() == $rn) {
                return $output;
            }
            if (Str::is($rn, Route::currentRouteName())) {
                return $output;
            }
        }
        return '';
    }
}


if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

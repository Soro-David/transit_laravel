<?php
return [
    'base' => env('CURRENCY_BASE', 'XOF'),
    'target' => env('CURRENCY_TARGET', 'EUR'),
    'api_url' => env('CURRENCY_API_URL', null),
    'api_key' => env('CURRENCY_API_KEY', null),
    'cache_ttl' => env('CURRENCY_CACHE_TTL', 3600),
    'fixed_rate' => env('CURRENCY_FIXED_RATE', null), // optionnel: définir un taux fixe
];

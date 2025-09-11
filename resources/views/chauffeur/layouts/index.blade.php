<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- IMPORTANT : CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- App Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')

    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]);
    </script>
</head>
<body>
    <div class="wrapper">
        @include('chauffeur.layouts.partials.navbar')
        @include('chauffeur.layouts.partials.sidebar')
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- jQuery (placer avant app.js si app.js l'utilise) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- App script (compilé) -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Optionnel : debug rapide pour vérifier que la meta csrf est bien présente -->
    <script>
        // console.log('CSRF meta token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        // console.log('Cookies:', document.cookie);
    </script>

    @stack('scripts')
</body>
</html>

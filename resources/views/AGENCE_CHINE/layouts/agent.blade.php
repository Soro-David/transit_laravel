<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <!-- Styles (CSS) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    
    <!-- Librairies externes (via CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.3/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Configuration globale -->
    <script>window.APP = @json([
        'currency_symbol' => config('settings.currency_symbol'),
        'warning_quantity' => config('settings.warning_quantity')
    ]);</script>

    @yield('css')

    <!-- Style du Loader -->
    <style>
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: Arial, sans-serif;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #loader p {
            font-size: 16px;
            color: #444;
            font-weight: bold;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
        <p>Chargement...</p>
    </div>

    <!-- Site wrapper -->
    <div class="wrapper">
        @include('AGENCE_CHINE.layouts.navbar')
        @include('AGENCE_CHINE.layouts.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('content-header')</h1>
                        </div>
                        <div class="col-sm-6 text-right">
                            @yield('content-actions')
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content" id="content" style="display: none;">
                @include('AGENCE_CHINE.layouts.partials.alert.success')
                @include('AGENCE_CHINE.layouts.partials.alert.error')
                @yield('content')
            </section>
        </div>

        <!-- Footer -->
        @include('AGENCE_CHINE.layouts.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <!-- Scripts (JS) -->
    
    <!-- jQuery doit être chargé en premier -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <!-- Fichier JS principal de votre application (compilé par Vite/Mix) -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Autres librairies externes (via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 JS -->
    
    <!-- Script du Loader -->
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.display = 'none';
            }
            const content = document.getElementById('content');
            if (content) {
                content.style.display = 'block';
            } else {
                console.warn('Element #content introuvable — vérifie ton layout.');
            }
        });
    </script>
    
    <!-- Scripts spécifiques à la page -->
    @yield('scripts')

</body>
</html>
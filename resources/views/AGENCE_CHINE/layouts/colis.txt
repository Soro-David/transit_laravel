<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts et styles -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- DataTables / UI / Select2 CSS (une seule inclusion chacune) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.3/css/responsive.dataTables.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script>window.APP = @json([
        'currency_symbol' => config('settings.currency_symbol'),
        'warning_quantity' => config('settings.warning_quantity')
    ]);</script>

    <!-- jQuery (charger avant les plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('css')
</head>

<body class="hold-transition sidebar-mini">
    <!-- Loader (déplacé DANS le <body>) -->
    <div id="loader">
        <div class="spinner"></div>
        <p class="text-center">Chargement...</p>
    </div>

    <!-- Site wrapper -->
    <div class="wrapper">
        @include('AGENCE_CHINE.layouts.navbar')
        @include('AGENCE_CHINE.layouts.sidebar')

        <!-- Content Wrapper (ajout d'un id 'content' pour le script du loader) -->
        <div id="content" class="content-wrapper">
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
            <section class="content">
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

    <!-- Scripts principaux (corrigés) -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- SUPPRIMEZ la ligne incorrecte suivante (elle causait des problèmes) -->
    {{-- <script src="'resources/js/app.js'"></script> --}}
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('jquery-ui-1.14.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('select2-4.1.0-beta.1/dist/js/select2.min.js') }}"></script>

    <!-- DataTables / extensions / autres libs (une seule inclusion de chaque) -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('js')

    <!-- Script loader sécurisé (dans le <body>, vérifie l'existence des éléments) -->
    <script>
        window.addEventListener('load', function() {
            // cache le loader si présent
            const loader = document.getElementById('loader');
            if (loader) loader.style.display = 'none';

            // affiche le contenu principal seulement si il existe
            const content = document.getElementById('content');
            if (content) {
                content.style.display = 'block';
            } else {
                console.warn('Element #content introuvable — vérifie ton layout.');
            }
        });
    </script>

</body>
</html>

<style>
/* Styles du loader (garder ou déplacer dans ton CSS) */
#loader { position: fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(255,255,255,.9); display:flex; justify-content:center; align-items:center; flex-direction:column; z-index:9999; }
.spinner { border:5px solid #f3f3f3; border-top:5px solid #3498db; border-radius:50%; width:60px; height:60px; animation: spin .8s linear infinite; margin-bottom:10px; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
#loader p { font-size:18px; color:#444; font-weight:bold; }
</style>

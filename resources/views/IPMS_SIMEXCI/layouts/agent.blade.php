<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts et styles -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.3/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    
    
    <!-- Scripts globaux -->
    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]);
    </script>

    <!-- jQuery (version unique) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('css')
</head>
<!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
        <p class="text-center">Chargement...</p>
    </div>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        @include('IPMS_SIMEXCI.layouts.navbar')
        @include('IPMS_SIMEXCI.layouts.sidebar')

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
            <section class="content">
                @include('IPMS_SIMEXCI.layouts.partials.alert.success')
                @include('IPMS_SIMEXCI.layouts.partials.alert.error')
                @yield('content')
            </section>
        </div>

        <!-- Footer -->
        @include('IPMS_SIMEXCI.layouts.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <!-- Scripts principaux -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="'resources/js/app.js'"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('jquery-ui-1.14.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('select2-4.1.0-beta.1/dist/js/select2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 


    @yield('js')

</body>

</html>
<script>
// Exécute le code après que la page soit complètement chargée
window.addEventListener('load', function() {
    // Cache le loader
    document.getElementById('loader').style.display = 'none';
    // Affiche le contenu
    document.getElementById('content').style.display = 'block';
});
</script>
<style>
/* Style général du loader */
#loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255, 255, 255, 0.9); /* Légère transparence */
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 9999;
    font-family: 'Arial', sans-serif;
    text-align: center;
    color: #555;
}

/* Style de l'animation */
.spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 0.8s linear infinite;
    margin-bottom: 10px;
}

/* Animation de rotation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Texte du loader */
#loader p {
    font-size: 18px;
    color: #444;
    font-weight: bold;
}


</style>

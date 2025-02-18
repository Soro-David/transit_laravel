<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- Datatables -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/datatables.min.css" rel="stylesheet">
    
    <!-- jQuery & SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" 
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" 
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" 
        integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous">
    </script>

    @yield('css')

    <script>
        window.APP = @json([
            'currency_symbol' => config('settings.currency_symbol'),
            'warning_quantity' => config('settings.warning_quantity')
        ]);
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
        <p class="text-center">Chargement...</p>
    </div>

    <div class="wrapper">
        @include('customer.layouts.partials.navbar')
        @include('customer.layouts.partials.sidebar')
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
            <section class="content">
                @yield('content')
            </section>
        </div>
        @include('customer.layouts.partials.footer')
        
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/datatables.min.js"></script>
    @yield('js')
</body>

</html>

<script>
// Exécute le code après que la page soit complètement chargée
window.addEventListener('load', function() {
    // Cache le loader
    document.getElementById('loader').style.display = 'none';
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
    background: rgba(255, 255, 255, 0.9); 
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

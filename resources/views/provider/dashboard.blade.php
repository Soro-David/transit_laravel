<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<!-- Log on to codeastro.com for more projects -->
    
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    @yield('css')
    <script>
        window.APP = <?php echo json_encode([
                            'currency_symbol' => config('settings.currency_symbol'),
                            'warning_quantity' => config('settings.warning_quantity')
                        ]) ?>
    </script>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('provider.layouts.navbar')
        @include('provider.layouts.sidebar')
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
                @include('layouts.partials.alert.success')
                @include('layouts.partials.alert.error')
                @yield('content')
            </section>
                    @section('content')
                    <div class="container-fluid">
                        <div class="row">
                        <!-- Log on to codeastro.com for more projects -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{$products_count}}</h3>
                                <p>{{ __('trans.total_products') }}</p>
                            </div>
                            <div class="icon">
                            <i class="fas fa-dolly-flatbed"></i>
                            </div>
                            <a href="{{route('products.index')}}" class="small-box-footer">{{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{$orders_count}}</h3>
                                <p>{{ __('trans.orders_count') }}</p>
                            </div>
                            <div class="icon">
                            <i class="fas fa-chart-line"></i>
                            </div>
                            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
                                <p>{{ __('trans.total_income') }}</p>
                            </div>
                            <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                            </div>
                            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        
                        
                    </div>

                    <div class="row"><!-- Log on to codeastro.com for more projects -->
                    <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-teal">
                            <div class="inner">
                                <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>

                                <p>{{ __('trans.todays_income') }}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-check-alt"></i>
                            </div>
                            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('trans.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->

                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{$customers_count}}</h3>

                            <p>{{ __('trans.total_customers') }}</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('customers.index') }}" class="small-box-footer">{{ __('trans.more_info') }}<i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    </div>
                </div><!-- Log on to codeastro.com for more projects -->
                @endsection
        </div>
        @include('layouts.partials.footer')
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('js')
</body>

</html>









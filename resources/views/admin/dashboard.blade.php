@extends('admin.layouts.admin')

@section('content-header', '')

@section('content')

    <div class="container-fluid">
      <h2 class="text-left"> Tableau de Bord</h2><br>
        <div class="row">
          <div class="col-lg-4 col-6">
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
          <div class="col-lg-4 col-6">
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
    </div>
    <div class="row">
    <div class="col-lg-4 col-6">
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
      <div class="col-lg-4 col-6">
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
    </div>
</div>
@endsection

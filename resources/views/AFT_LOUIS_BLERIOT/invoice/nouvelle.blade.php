@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form action="{{ route('aftlb_invoice.store.invoice') }}" method="POST">
        @csrf
        <div class="form-container text-center">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-6 mb-3">
                    <label for="reference_colis" class="form-label">
                        Saisissez la référence du colis
                    </label>
                    <input type="text" name="reference_colis" id="reference_colis" 
                           value="{{ old('reference_colis') }}" class="form-control" required>
                </div>
                <div class="col-sm-12 col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Recherche</button>
                </div>
            </div>
        </div>
    </form>
    
</section>
@endsection
<style>
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
</style>

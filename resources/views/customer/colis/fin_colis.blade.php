@extends('customer.layouts.index')

@section('content-header')
@endsection

@section('content')
<section class="p-4 mx-auto">
    <div class="form-container text-center">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h2>Votre colis a été enregistré avec succès !</h2>
        <p>Merci pour votre confiance. Vous pouvez suivre l'état de votre colis depuis votre espace client.</p>

        @if(isset($qrCodeUrl))
            <div class="qr-code my-4">
                <img src="{{ asset($qrCodeUrl) }}" alt="QR Code du colis">
            </div>
        @endif

        {{-- <a href="{{ route('customer.colis.suivi') }}" class="btn btn-primary mt-4">Suivre mon colis</a> --}}
    </div>
</section>

{{-- CSS Personnalisé --}}
<style>
    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    body {
        background-color: #f7f7f7;
    }
    .qr-code img {
        max-width: 200px;
        height: auto;
    }
</style>
@endsection

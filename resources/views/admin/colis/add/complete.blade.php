@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
    @csrf
    <section>
        <img src="{{ asset('images/colis_57.png') }}" alt="Logo" class="img-fluid custom-logo" style="max-height: 90px;">
            <h3>Votre QR Code :</h3>
            <!-- Affichage de l'image du QR Code -->
            <img src="{{ asset('storage/' . $filePath) }}" alt="QR Code" style="max-width: 300px;">
        </div>
    </section>

    <style>
        section {
            background-color: #fff !important;
        }

        table.dataTable {
            width: 100% !important;
        }

        table.dataTable th,
        table.dataTable td {
            white-space: nowrap;
        }
    </style>
@endsection

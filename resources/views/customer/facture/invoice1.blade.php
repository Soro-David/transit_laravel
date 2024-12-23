@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
<div class="container">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Facture</h4><br>
                        <div id="products-container">
                            <table id="productTable" class="display">
                                <thead>
                                    <tr>
                                        <th>Reference du colis</th>
                                        <th>Date de creation</th>
                                        <th>Status</th>
                                        <th> Destinataire</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<!-- Script JavaScript -->
<script>
  $(document).ready(function() {
    var table = $("#productTable").DataTable({
        responsive: true, // Rend la table responsive
        language: {
            url: "//cdn.datatables.net/plug-ins/1.12.1/i18n/fr-FR.json"
        },
        ajax: {
            url: '{{ route("customer_colis.get.facture") }}',
            // type: 'GET', // Méthode HTTP pour récupérer les données
            // dataType: 'json' // Type de données attendu
        },
        columns: [
            
            { data: 'agence_expedition', name: 'agence_expedition' },
            { data: 'agence_destination', name: 'agence_destination' },
            { data: 'status', name: 'status' },
            { data: 'etat', name: 'etat' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false // Actions non triables et non recherchables
            }
        ]
    })
    });
</script>
@endsection

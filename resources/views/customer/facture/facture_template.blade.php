@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
<div class="container">
        <h1>Facture</h1>
        <p><strong>Date :</strong> {{ $date }}</p>
        <table>
            <tr>
                <th>Champ</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td>Nom de l'expéditeur</td>
                <td>{{ $nom_expediteur }}</td>
            </tr>
            <tr>
                <td>Prénom de l'expéditeur</td>
                <td>{{ $prenom_expediteur }}</td>
            </tr>
            <tr>
                <td>Email de l'expéditeur</td>
                <td>{{ $email_expediteur }}</td>
            </tr>
            <tr>
                <td>Agence d'expédition</td>
                <td>{{ $agence_expedition }}</td>
            </tr>
            <tr>
                <td>Agence de destination</td>
                <td>{{ $agence_destination }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $status }}</td>
            </tr>
            <tr>
                <td>État</td>
                <td>{{ $etat }}</td>
            </tr>
        </table>
    
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
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
@endsection

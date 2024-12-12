@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
<section class="py-3">
    <div class="container">
        <form action="" method="POST" class="mt-4">
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <h4 class="text-left mt-4">Liste des colis en attente</h4><br>
                            <div id="products-container">
                                <div class="table-responsive">
                                    <table id="productTable" class="table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>Nom Expéditeur</th>
                                                <th>Contact Expéditeur</th>
                                                <th>Agence Expéditeur</th>
                                                <th>Nom Destinataire</th>
                                                <th>Contact Destinataire</th>
                                                <th>Agence Destinataire</th>
                                                <th>Etat du Colis</th>
                                                <th>Date de Création</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="container">
                                    <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Script JavaScript -->
    <script>
        $(document).ready(function () {
            var table = $("#productTable").DataTable({
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json"
                },
                ajax: '{{ route('colis.get.colis.hold') }}',
                columns: [
                    { data: 'nom_expediteur', name: 'nom_expediteur' },
                    { data: 'tel_expediteur', name: 'contact_expediteur' },
                    { data: 'agence_expedition', name: 'agence_expedition' },
                    { data: 'nom_destinataire', name: 'nom_destinataire' },
                    { data: 'tel_destinataire', name: 'contact_destinataire' },
                    { data: 'agence_destination', name: 'agence_destination' },
                    { data: 'etat', name: 'etat' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
</section>
@endsection

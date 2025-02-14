@extends('admin.layouts.admin')

@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>Historique des Factures</h2>
            <div class="table-responsive">
                <table class="table table-bordered" id="invoice-table">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Expéditeur</th>
                            <th>Date de Création</th>
                            <th>Agent</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('#invoice-table').DataTable({                
            processing: true,
            serverSide: true,
            language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier de langue
            },
            ajax: '{{ route('invoice.get.invoice.historique') }}',
            columns: [
                { data: 'numero_facture', name: 'numero_facture' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_expediteur + ' ' + row.prenom_expediteur;  // Affiche le nom et prénom de l'expéditeur
                    }
                },
                { data: 'date_creation', name: 'date_creation' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return row.nom_agent + ' ' + row.prenom_agent;  // Affiche le nom et prénom de l'agent
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection

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
                            <th>Destinataire</th>
                            <th>Agent</th>
                            <th>Date facture</th>
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
                { data: 'nom_expediteur', name: 'nom_expediteur' },
                { data: 'nom_destinataire', name: 'nom_destinataire' },
                { data: 'nom_agent', name: 'nom_agent' },
                { data: 'date_creation', name: 'date_creation' },
                
            ]
        });
    });
</script>
@endsection

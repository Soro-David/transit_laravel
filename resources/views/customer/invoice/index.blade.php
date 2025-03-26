@extends('customer.layouts.index')

@section('content-header')
    <h1>Factures</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liste des factures</h3>
                    </div>
                    <div class="card-body">
                        <table id="invoicesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>Numéro de Facture</th> --}}
                                    <th>Date de Création</th>
                                    <th>Expéditeur</th>
                                    <th>Destinataire</th>
                                    {{-- <th>Agent</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        {{-- <td>{{ $invoice->invoice_number }}</td> --}}
                                        <td>{{ $invoice->created_at }}</td>
                                        <td>{{ $invoice->expediteur->nom ?? 'N/A' }} {{ $invoice->expediteur->prenom ?? 'N/A' }} </td>
                                        <td>{{ $invoice->destinataire->nom ?? 'N/A' }} {{ $invoice->destinataire->prenom ?? 'N/A' }}</td>
                                        {{-- <td>{{ $invoice->agent->nom ?? 'N/A' }}</td> --}}
                                        <td>
                                            <a href="{{ route('customer_colis.edit.invoice', ['id' => $invoice->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-print"></i>Afficher</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
  $(function () {
    $("#invoicesTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#invoicesTable_wrapper .col-md-6:eq(0)');
  });
</script>
@endpush
@extends('admin.layouts.admin')

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm">
                <h4 class="mb-4">Historique des Conteneurs</h4>

                {{-- Sélecteur pour filtrer par agence --}}
                <div class="form-group mb-4" style="max-width: 350px;">
                    <label for="agenceFilter" class="form-label"><strong>Filtrer par agence</strong></label>
                    <select id="agenceFilter" class="form-control form-select">
                        <option value="">Toutes les agences</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence }}">{{ $agence }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="table-responsive">
                    <table id="contenairesTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Référence du Conteneur</th>
                                <th>Agence</th>
                                <th class="text-center" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Le contenu sera chargé par DataTables via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    var table = $('#contenairesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: {
            url: '{{ route("cargaison.get.contenaires") }}',
            data: function (d) {
                d.agence = $('#agenceFilter').val();
            }
        },
        columns: [
            { data: 'reference_contenaire', name: 'reference_contenaire' },
            { data: 'agence', name: 'agence' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    $('#agenceFilter').on('change', function () {
        table.ajax.reload();
    });
});
</script>


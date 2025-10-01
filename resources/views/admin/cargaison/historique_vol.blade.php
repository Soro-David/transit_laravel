@extends('admin.layouts.admin')

@section('content')
<section class="py-3">
    <div class="row">
        <div class="col-12">
            <div class="border p-4 rounded shadow-sm">
                {{-- **** CORRECTION : Titre harmonisé **** --}}
                <h4 class="mb-4">Historique des Vols</h4>

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
                    {{-- **** CORRECTION : ID du tableau harmonisé **** --}}
                    <table id="volsTable" class="table table-bordered table-striped display" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                {{-- **** CORRECTION : Titre de colonne harmonisé **** --}}
                                <th>Référence du Vol</th>
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

{{-- @push('scripts') --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    // **** CORRECTION : Sélecteur du tableau mis à jour ****
    var table = $('#volsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        language: { url: "{{ asset('js/fr-FR.json') }}" },
        ajax: {
            url: '{{ route("cargaison.get.vol") }}',
            data: function (d) {
                d.agence = $('#agenceFilter').val(); // Le filtre fonctionne correctement
            }
        },
        columns: [
            // **** CORRECTION CRITIQUE : Le nom de la donnée doit correspondre à ce que le contrôleur envoie ('reference_vol') ****
            { data: 'reference_vol', name: 'reference_vol' },
            { data: 'agence', name: 'agence' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    // Recharge la table lorsque le filtre change
    $('#agenceFilter').on('change', function () {
        table.ajax.reload();
    });
});
</script>
{{-- @endpush --}}
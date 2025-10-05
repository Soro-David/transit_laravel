@extends('admin.layouts.admin')

@section('content')
<section class="content">
    <h1 class="mb-4">📋 Liste des Prospects</h1>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createProspectModal">
        ➕ Ajouter un nouveau prospect
    </button>

    <div class="card shadow-sm">
        <div class="card-body">
            <table id="prospectsTable" class="table table-bordered table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal Ajout Prospect -->
<div class="modal fade" id="createProspectModal" tabindex="-1" aria-labelledby="createProspectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="createProspectModalLabel">➕ Créer un nouveau Prospect</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('prospects.store') }}" method="POST">
          @csrf
          <div class="row mb-3">
              <div class="col-md-6">
                  <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="nom" name="nom" required>
              </div>
              <div class="col-md-6">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input type="text" class="form-control" id="prenom" name="prenom">
              </div>
          </div>
          <div class="mb-3">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="row mb-3">
              <div class="col-md-6">
                  <label for="tel" class="form-label">Téléphone</label>
                  <input type="text" class="form-control" id="tel" name="tel">
              </div>
              <div class="col-md-6">
                  <label for="adresse" class="form-label">Adresse</label>
                  <input type="text" class="form-control" id="adresse" name="adresse">
              </div>
          </div>
          <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-success me-2"><i class="fas fa-save"></i> Enregistrer</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- DataTables Core --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>

{{-- DataTables Buttons & Export --}}
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script>
$(document).ready(function() {
    $('#prospectsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('prospects.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'numero', name: 'numero' },
            { data: 'type', name: 'type' },
            { data: 'created_at', name: 'created_at' }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr_fr.json",
            search: "🔍 Rechercher :",
            paginate: { previous: "Précédent", next: "Suivant" },
            zeroRecords: "Aucun prospect trouvé",
            processing: "Chargement..."
        },
        pageLength: 10,
        order: [[1, "desc"]],
        responsive: true,
        autoWidth: false,

        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: '🖨️ Imprimer',
                exportOptions: { columns: ':visible' }
            },
            // {
            //     extend: 'pdfHtml5',
            //     text: '📄 PDF',
            //     orientation: 'landscape',
            //     pageSize: 'A4',
            //     exportOptions: { columns: ':visible' }
            // },
            // {
            //     extend: 'excelHtml5',
            //     text: '📊 Excel',
            //     exportOptions: { columns: ':visible' }
            // }
        ]
    });
});
</script>

@extends('admin.layouts.admin')

@section('content')
<section class="content">
    <h1>Liste des Prospects</h1>

    <!-- Bouton pour ouvrir le modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createProspectModal">
        ➕ Ajouter un nouveau prospect
    </button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table id="prospectsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        {{-- <th>Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($allProspects as $prospect)
                        <tr>
                            <td>{{ $prospect->id }}</td>
                            <td>{{ $prospect->full_name }}</td>
                            <td>{{ $prospect->email }}</td>
                            <td>{{ $prospect->tel }}</td>
                            <td>{{ $prospect->type == 'expediteur_non_user' ? 'Expéditeur non utilisateur' : 'Prospect' }}</td>

                        {{-- <td>
                            <!-- Voir -->
                            <a href="{{ route('prospects.show', $prospect->id) }}" 
                            class="btn btn-info btn-sm" 
                            title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- Modifier -->
                            <a href="{{ route('prospects.edit', $prospect->id) }}" 
                            class="btn btn-warning btn-sm" 
                            title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Supprimer -->
                            @if($prospect->type == 'prospect_table')
                                <form action="{{ route('prospects.destroy', $prospect->id) }}" 
                                    method="POST" 
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger btn-sm" 
                                            title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce prospect ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </td> --}}

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="createProspectModal" tabindex="-1" aria-labelledby="createProspectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> {{-- modal large --}}
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
                  <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                         id="nom" name="nom" value="{{ old('nom') }}" required>
                  @error('nom')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>

              <div class="col-md-6">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                         id="prenom" name="prenom" value="{{ old('prenom') }}">
                  @error('prenom')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
          </div>

          <div class="mb-3">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" 
                     id="email" name="email" value="{{ old('email') }}" required>
              @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>

          <div class="row mb-3">
              <div class="col-md-6">
                  <label for="tel" class="form-label">Téléphone</label>
                  <input type="text" class="form-control @error('tel') is-invalid @enderror" 
                         id="tel" name="tel" value="{{ old('tel') }}">
                  @error('tel')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>

              <div class="col-md-6">
                  <label for="adresse" class="form-label">Adresse</label>
                  <input type="text" class="form-control @error('adresse') is-invalid @enderror" 
                         id="adresse" name="adresse" value="{{ old('adresse') }}">
                  @error('adresse')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
          </div>

          <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-success me-2">
                  <i class="fas fa-save"></i> Enregistrer
              </button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times"></i> Annuler
              </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')

<script>
    $(document).ready(function() {
        $('#prospectsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr_fr.json"
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "ordering": true,
            "order": [[0, "desc"]],
            "searching": true,
            "paging": true
        });
    });
</script>
@endsection

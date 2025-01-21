@extends('admin.layouts.admin')

@section('content-header')
    <h4 class="text-center">Édition des Programmes</h4>
@endsection

@section('content')
<section class="p-4 mx-auto">
    <form id="update-form" action="" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Référence Colis</th>
                        <th>Status</th>
                        <th>Dernière Mise à Jour</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programmes as $programme)
                        <tr>
                            <td>{{ $programme->colis->reference_colis ?? 'N/A' }}</td>
                            <td>{{ $programme->status }}</td>
                            <td>{{ $programme->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-start gap-2 mt-4">
            <!-- Bouton Retour -->
            <button type="button" class="btn btn-secondary" id="return-btn">
                <i class="fas fa-arrow-left" style="font-size: 18px; margin-right: 5px;"></i> Retour
            </button>
            <!-- Bouton Mise à jour -->
            <button type="button" id="validate-btn" class="btn btn-primary">Valider</button>
        </div>
    </form>
</section>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('validate-btn').addEventListener('click', function () {
        Swal.fire({
            title: 'Confirmer la mise à jour',
            text: "Êtes-vous sûr de vouloir mettre à jour le prix de transit du colis ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Oui, j'accepte",
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Soumet le formulaire si l'utilisateur confirme
                document.getElementById('update-form').submit();
            }
        });
    });

    // Fonction pour revenir à la page précédente
    document.getElementById('return-btn').addEventListener('click', function () {
        window.history.back();
    });
</script>

{{-- CSS Personnalisé --}}
<style>
    body {
        background-color: #f7f7f7;
    }

    .form-container {
        max-width: 95%;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
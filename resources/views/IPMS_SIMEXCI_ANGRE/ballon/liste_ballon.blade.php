@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-3">

    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 rounded shadow-sm">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="card-title mb-0 fw-bold">Informations du Véhicule de Navigation</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            @foreach ($ballons as $ballon)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-ship fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title fw-bold">{{ $ballon->reference_bateau }}</h5>
                                            <p class="card-text text-muted">📅 Date d'arrivée : <strong>{{ $ballon->date_arriver }}</strong></p>

                                            <button class="btn-valider"
                                                    data-bateau-id="{{ $ballon->id }}"
                                                    id="validerBtn-{{ $ballon->id }}"
                                                    data-reference-conteneur="{{ $ballon->reference_conteneur }}"
                                                    onclick="validerBallon(this)">
                                                <i class="fas fa-check-circle"></i> Valider
                                            </button>
                                    

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($ballons->isEmpty())
                            <p class="text-center text-muted mt-3">Aucun bateau trouvé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                <div id="products-container">
                    <div class="table-responsive">
                        <table id="productTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Référence Ballon</th>
                                    <th>Date départ</th>
                                    <th>Date arrivée</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        // DataTable des ballons
        const table = $("#productTable").DataTable({
            responsive: true,
            language: { url: "{{ asset('js/fr-FR.json') }}" },
            ajax: '{{ route('ipms_angre_colis.get.ballon') }}',
            columns: [
                { data: 'reference_bateau' },
                { data: 'date_depart' },
                { data: 'date_arriver' },
                // { data: 'action', orderable: false, searchable: false }
            ],
        });

        // Redirection vers la page des colis
        $('#productTable').on('click', '.voir-colis', function () {
            const reference = $(this).data('reference');
            // La route doit être du type edit_ballon avec paramètre reference_vol
             const url = '{{ route("ipms_angre_colis.edit_colis_ballon") }}' + '?reference_vol=' + reference;
             
            window.location.href = url;
        });
    });

</script>

</section>
<style>
    .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
    }

    .dataTable-wrapper {
        width: 80% !important;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .dt-button {
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid transparent;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-valider {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: #28a745; /* Vert Bootstrap */
        color: white;
        font-size: 18px;
        font-weight: bold;
        padding: 12px 20px;
        border: none;
        border-radius: 50px; /* Forme bien arrondie */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        text-transform: uppercase;
    }

    .btn-valider:hover {
        background-color: #218838; /* Légèrement plus foncé au survol */
        transform: scale(1.05);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
    }

    .btn-valider:active {
        transform: scale(0.95);
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-valider i {
        font-size: 20px;
    }

</style>
@endsection


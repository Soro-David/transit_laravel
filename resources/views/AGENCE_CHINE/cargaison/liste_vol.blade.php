@extends('AGENCE_CHINE.layouts.agent')
@section('content-header')
@section('content')
<section class="py-3">
    <h2 class="">Colis dans le vol</h2>
    <form action="{{route('chine_colis.contenaire.fermer')}}" method="POST" class="mt-4">
        @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <div id="products-container">
                                <div class="table-responsive">
                                    <div>
                                        <label for="reference_contenaire" style="font-size: 25px;">CONTENEUR REF:</label>
                                        <strong style="font-size: 30px;">{{ $referenceVol }}</strong>
                                    </div>
                                    <table id="productTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>référence</th>
                                                <th>Nombre de colis</th>
                                                <th>Agence d'expédition</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <input type="hidden" name="colis_data" id="colis_data">
                                    <div class="container text-right">
                                        <button type="submit" class="btn btn-danger mt-3" id="btnFermerVol">
                                             Terminer le Vol
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
    </form>
</section>
<!-- Script JavaScript -->
<script>
   $(document).ready(function () {
    var table = $("#productTable").DataTable({
        responsive: true,
        language: {
                url: "{{ asset('js/fr-FR.json') }}" // Chemin local vers le fichier
            },
        ajax: '{{ route('chine_colis.get.colis.vol') }}',
        columns: [
            { data: 'reference_colis' },
            { data: 'nombre_de_colis' },
            { data: 'expediteur_agence' },
            {data: 'created_at',
            render: function(data, type, row) {
                    if (data) {
                        const date = new Date(data);
                        const day = ('0' + date.getDate()).slice(-2);
                        const month = ('0' + (date.getMonth() + 1)).slice(-2);
                        const year = date.getFullYear().toString().slice(-2);
                        const hours = ('0' + date.getHours()).slice(-2);
                        const minutes = ('0' + date.getMinutes()).slice(-2);
                        return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                    }
                    return data;
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
    });
    $('#btnFermerVol').click(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Êtes-vous sûr ?",
            text: "Voulez-vous vraiment fermer ce conteneur ? Cette action est irréversible.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Oui, fermer",
            cancelButtonText: "Annuler"
        }).then((result) => {
            if (result.isConfirmed) {
                $('form').submit(); // Soumission du formulaire après confirmation
            }
        });
    });
});

</script>

<style>
    table.dataTable {
        width: 100% !important;
    }
    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }
</style>
@endsection

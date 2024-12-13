@extends('customer.layouts.index')
@section('content-header')
@section('content')
<section class="py-3">
<div class="container">
    <form action="" method="POST" class="mt-4">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                        <h4 class="text-left mt-4">Historique des colis</h4><br>
                        <div id="products-container">
                            <div class="table-responsive">
                                <table id="productTable" class="display">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Expéditeur</th>
                                            <th>Quantité</th>
                                            <th>Dimensions</th>
                                            <th>Prix</th>
                                            <th>Status</th>
                                            <th> Destinataire</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        <h6 class="text-right mt-4">Prix total : <span id="prix-total">0</span> FCFA</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<!-- Script JavaScript -->

<script>
    $(document).ready(function() {
    const table = $("#productTable").DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/fr-FR.json"
        }
    });
    $(".add-product").on("click", function() {
        var description = $("#description").val();
        var quantite = $("#quantite").val();
        var dimension = $("#dimension").val();
        var prix = $("#prix").val();

        if (description && quantite && dimension && prix) {
            $.ajax({
                url: '{{ route("colis.store") }}',
                method: "POST",
                data: {
                    description: description,
                    quantite: quantite,
                    dimension: dimension,
                    prix: prix,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    table.row
                        .add([
                            response.description,
                            response.quantite,
                            response.dimension,
                            response.prix
                        ])
                        .draw(false);
                }
            });
        }
    });
});
        
</script>


@endsection

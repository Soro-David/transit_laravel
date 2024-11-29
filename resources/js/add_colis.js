$(document).ready(function() {
    var table = $("#productTable").DataTable();
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

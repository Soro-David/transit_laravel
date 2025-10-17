        @extends('AFT_LOUIS_BLERIOT.layouts.agent')
        @section('content-header')
        {{-- CSRF Token pour les requêtes AJAX --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Font Awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        {{-- jQuery --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        @endsection
        
        @section('content')
        <section class="py-3">
        
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                            <div id="products-container">
                                <div class="table-responsive">
                                    <div>
                                        <label for="reference_contenaire" style="font-size: 25px;">CONTENEUR REF:</label>
                                        <strong style="font-size: 30px;">{{ $referenceContenaire }}</strong>
                                    </div>
                                    <table id="productTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Référence</th>
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
                                    <div class="container text-right">
                                        <form id="fermerConteneurForm" action="{{ route('aftlb_colis.contenaire.fermer') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger mt-3" id="btnFermerConteneur">
                                                Fermer le conteneur
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
        </section>
        <!-- Script JavaScript -->
        <script>
        $(document).ready(function () {
            // Initialiser DataTable pour afficher les colis
            var table = $("#productTable").DataTable({
                responsive: true,
                language: {
                    url: "{{ asset('js/fr-FR.json') }}"
                },
                ajax: '{{ route('aftlb_colis.get.colis.contenaire') }}',
                columns: [
                    { data: 'reference_colis' },
                    { data: 'nombre_de_colis' },
                    { data: 'expediteur_agence' },
                    {data: 'created_at',
                    render: function(data) {
                        
                        return data;
                    }},
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        
            // Gestion du bouton pour fermer le conteneur avec confirmation
            $('#btnFermerConteneur').click(function (e) {
                e.preventDefault();  // Empêcher la soumission du formulaire avant la confirmation
        
                // Afficher la confirmation avec SweetAlert
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
                    // Si l'utilisateur confirme, soumettre le formulaire
                    if (result.isConfirmed) {
                    // console.log("n,bb,nhk")
                    $('#fermerConteneurForm').submit();
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
        
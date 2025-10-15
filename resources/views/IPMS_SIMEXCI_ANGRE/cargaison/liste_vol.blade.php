@extends('IPMS_SIMEXCI_ANGRE.layouts.agent')
@section('content-header')
        @section('content')
        <section class="py-3">
            <h2 class="">Colis dans le Ballon</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="border p-4 rounded shadow-sm" style="border-color: #ffa500;">
                                    <div id="products-container">
                                        <div class="table-responsive">
                                            <div>
                                                <label for="reference_contenaire" style="font-size: 25px;">VOL REF:</label>
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
                                            <div class="container text-right">
                                                <form id="btnFermerVol" action="{{ route('ipms_angre_colis.vol.fermer') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger mt-3" id="btnFermerConteneur">
                                                        Fermer le ballon
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
            var table = $("#productTable").DataTable({
                responsive: true,
                language: {
                        url: "{{ asset('js/fr-FR.json') }}"
                    },
                ajax: '{{ route('ipms_angre_colis.get.colis.vol') }}',
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
                // Gestion du bouton de fermeture du conteneur avec SweetAlert
                $('#btnFermerVol').click(function (e) {
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
                    if (result.isConfirmed) {
                    $('#btnFermerVol').submit();
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
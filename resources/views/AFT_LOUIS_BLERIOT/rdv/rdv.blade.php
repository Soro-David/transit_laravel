@extends('AFT_LOUIS_BLERIOT.layouts.agent')

@section('content-header')
    <h1>Gestion des Rendez-vous</h1>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title">Liste des Rendez-vous</h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="rdvTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="depot-tab" data-toggle="tab" href="#depot" role="tab" aria-controls="depot" aria-selected="true">RDV Dépôt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="recuperation-tab" data-toggle="tab" href="#recuperation" role="tab" aria-controls="recuperation" aria-selected="false">RDV Récupération</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="voler-tab" data-toggle="tab" href="#voler" role="tab" aria-controls="voler" aria-selected="false">RDV Livraison</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="rdvTabsContent">
                    <!-- Tab Dépôt -->
                    <div class="tab-pane fade show active" id="depot" role="tabpanel" aria-labelledby="depot-tab">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom Destinataire</th>
                                    <th>Référence Colis</th>
                                    <th>Téléphone Destinataire</th>
                                    <th>Date</th>
                                    <th>Lieu Destination</th>
                                    <th>Chauffeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depotRdvs as $rdv)
                                    <tr>
                                        <td>{{ $rdv->nom_destinataire }}</td>
                                        <td>{{ $rdv->reference_colis }}</td>
                                        <td>{{ $rdv->tel_destinataire }}</td>
                                        <td>{{ $rdv->date_programme }}</td>
                                        <td>{{ $rdv->lieu_destination }}</td>
                                        <td>{{ $rdv->chauffeur->nom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($depotRdvs->isEmpty())
                            <p>Aucun RDV de dépôt trouvé.</p>
                        @endif
                    </div>

                    <!-- Tab Récupération -->
                    <div class="tab-pane fade" id="recuperation" role="tabpanel" aria-labelledby="recuperation-tab">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom Expéditeur</th>
                                    <th>Référence Colis</th>
                                    <th>Téléphone Expéditeur</th>
                                    <th>Date</th>
                                    <th>Lieu Expédition</th>
                                    <th>Chauffeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recuperationRdvs as $rdv)
                                    <tr>
                                        <td>{{ $rdv->nom_expediteur }}</td>
                                        <td>{{ $rdv->reference_colis }}</td>
                                        <td>{{ $rdv->tel_expediteur }}</td>
                                        <td>{{ $rdv->date_programme }}</td>
                                        <td>{{ $rdv->lieu_expedition }}</td>
                                        <td>{{ $rdv->chauffeur->nom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($recuperationRdvs->isEmpty())
                            <p>Aucun RDV de récupération trouvé.</p>
                        @endif
                    </div>

                    <!-- Nouveau Tab Voler Livraison -->
                    <div class="tab-pane fade" id="voler" role="tabpanel" aria-labelledby="voler-tab">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom Expéditeur</th>
                                    <th>Référence Colis</th>
                                    <th>Téléphone Expéditeur</th>
                                    <th>Date</th>
                                    <th>Lieu de Vol</th>
                                    <th>Chauffeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($livraisonRdvs as $rdv)
                                    <tr>
                                        <td>{{ $rdv->nom_expediteur }}</td>
                                        <td>{{ $rdv->reference_colis }}</td>
                                        <td>{{ $rdv->tel_expediteur }}</td>
                                        <td>{{ $rdv->date_programme }}</td>
                                        <td>{{ $rdv->lieu_expedition }}</td>
                                        <td>{{ $rdv->chauffeur->nom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($livraisonRdvs->isEmpty())
                            <p>Aucun RDV de vol trouvé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#rdvTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endsection
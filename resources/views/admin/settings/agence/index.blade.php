@extends('admin.layouts.admin')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<ul class="horizontal-nav">
    <li class="active"><a href="{{route('setting.agence.index')}}">Agences</a></li>
    <li><a href="{{route('setting.chauffeur.index')}}">Chauffeurs</a></li>
</ul>
<div class="card mx-auto mt-5" style="max-width: 800px;"> <!-- Augmenter la largeur de la carte -->
    <div class="card-header text-center">
        <h5>Paramètre Agents</h5>
    </div>
    <div class="card-body" style="padding: 30px;">
        <form>
            <div class="mb-3 text-center">
                <label for="mode_transit" class="form-label">Sélectionnez Un Agent</label>
                <select name="mode_transit" id="mode_transit" class="form-select" style="max-width: 100%; margin: 0 auto;">
                    <option value="" disabled selected>-- Sélectionnez Un Agent --</option>
                    <option value="1">Maritime</option>
                    <option value="2">Aérien</option>
                </select>
            </div>
        </form>

        <!-- Formulaire à afficher ou masquer selon la sélection -->
        <div id="agencyForm" style="display: none;">
            <form>
                <div class="mb-3">
                    <label for="agencyName" class="form-label">Nom de l'Agence</label>
                    <input type="text" id="agencyName" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="agencyRoles" class="form-label">Rôles Disponibles</label>
                    <input type="text" id="agencyRoles" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="agencyPermissions" class="form-label">Permissions</label>
                    <textarea id="agencyPermissions" class="form-control" rows="3" readonly></textarea>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Lorsque la sélection de l'agent change
        $('#mode_transit').on('change', function () {
            const selectedAgent = $(this).val();

            if (selectedAgent) {
                // Afficher le formulaire si un agent est sélectionné
                $('#agencyForm').show();

                // Changer le contenu du formulaire selon l'agent sélectionné
                if (selectedAgent == '1') {
                    $('#agencyName').val('Agence Maritime');
                    $('#agencyRoles').val('Rôle : Gestion Maritime');
                    $('#agencyPermissions').val('Permissions : Accès aux ports, Accès aux navires');
                } else if (selectedAgent == '2') {
                    $('#agencyName').val('Agence Aérienne');
                    $('#agencyRoles').val('Rôle : Gestion Aérienne');
                    $('#agencyPermissions').val('Permissions : Accès aux aéroports, Accès aux avions');
                }
            } else {
                // Masquer le formulaire si aucun agent n'est sélectionné
                $('#agencyForm').hide();
            }
        });

        // Changer la couleur de l'élément actif dans la barre de navigation
        $('.horizontal-nav li a').on('click', function () {
            $('.horizontal-nav li').removeClass('active');
            $(this).closest('li').addClass('active');
        });
    });
</script>

<style>
    /* CSS pour la barre de navigation */
    .horizontal-nav {
        display: flex;
        list-style-type: none;
        margin: 0;
        padding: 0;
        background-color: #2c3e50;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .horizontal-nav li {
        flex: 1; /* Chaque élément occupe un espace égal */
        margin: 0;
    }

    .horizontal-nav li a {
        display: block;
        padding: 10px 20px;
        color: white;
        text-decoration: none; /* Pas de soulignement */
        font-size: 16px;
        border-radius: 0px;
        text-align: center;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Changer l'apparence de l'élément actif */
    .horizontal-nav li.active a {
        background-color: white;
        color: black; /* Texte noir */
    }

    /* Effet au survol */
    .horizontal-nav li a:hover {
        background-color: white; /* Fond blanc au survol */
        color: black; /* Texte noir */
        text-decoration: underline; /* Soulignement du texte */
    }
</style>
@endsection

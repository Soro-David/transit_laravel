<!DOCTYPE html>
<html>
<head>
    <title>Liste des Programmes</title>
    <style>
        @page { margin: 20px; size: A4 landscape; }

        /* Alignement logo + texte */
        .header {
            display: flex;
            align-items: center; /* Centrage vertical */
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .logo-container {
            margin-right: 20px; /* Espace entre le logo et le texte */
        }

        .logo {
            width: 120px;
            height: auto;
            margin-top: 0;
        }


        .company-info {
            flex-grow: 1; /* Permet au texte de prendre l'espace restant */
            margin: 0;
            padding: 0;
        }

        .company-info h2 {
            margin: 0;
            padding: 0;
            font-size: 20px;
            line-height: 1.2;
        }

        .company-info p {
            margin: 5px 0 0 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.2;
        }


        /* Ajustement tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/LOGOAFT.png') }}" class="logo">
        </div>
        <div class="company-info">
            <h2>AFT IMPORT EXPORT</h2>
            <p>
                7 Avenue Louis BLERIOT<br>
                93120 LA COURNEUVE<br>
                Tél: 01 86 78 69 67
            </p>
        </div>
    </div>


    <!-- Tableau des programmes -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%">Date</th>
                <th style="width: 10%">Chauffeur</th>
                <th style="width: 10%">Réf. Colis</th>
                <th style="width: 8%">Action</th>
                <th style="width: 12%">Expéditeur</th>
                <th style="width: 10%">Tél Expéditeur</th>
                <th style="width: 10%">Adresse Expédition</th>
                <th style="width: 12%">Destinataire</th>
                <th style="width: 10%">Tél Destinataire</th>
                <th style="width: 10%">Adresse Destination</th>
                <th style="width: 8%">État RDV</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programmes as $programme)
            <tr>
                <td>{{ $programme->date_programme }}</td>
                <td>{{ $programme->chauffeur->nom ?? 'N/A' }}</td>
                <td>{{ $programme->reference_colis }}</td>
                <td>{{ $programme->actions_a_faire }}</td>
                <td>{{ $programme->nom_expediteur }}</td>
                <td>{{ $programme->tel_expediteur }}</td>
                <td>{{ $programme->Adresse_expedition }}</td>
                <td>{{ $programme->nom_destinataire }}</td>
                <td>{{ $programme->tel_destinataire }}</td>
                <td>{{ $programme->Adresse_destination }}</td>
                <td>{{ $programme->etat_rdv }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
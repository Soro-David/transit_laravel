@extends('agent.layouts.agent')
@section('content-header')
@endsection

@section('content')
    @csrf
    <section>
        <div class="container text-center">
            <h1>QR Code</h1>
            <img class="imageqr" src="{{ asset($filePath) }}" alt="QR Code" style="max-width: 300px; margin: 0 auto; display: block;">
            <button class="btn btn-primary mt-4" onclick="printImage()">Imprimer</button>
             <!-- Nouvelle div pour le bouton de retour -->
            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
            </div>
        </div>
        
    </section>

    <style>
        .btn {
        width: 15%;
        height: 40px;
        font-size: 18px;
    }
        section {
            background-color: #fff !important;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .imageqr {
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        @media print {
            body * {
                visibility: hidden; /* Masquer tout le contenu */
            }

            .imageqr {
                visibility: visible; /* Montrer uniquement l'image */
                display: block;
                margin: 0 auto;
            }

            .imageqr::after {
                content: ''; /* Empêche les éléments adjacents d'être affichés */
            }
        }
    </style>

    <script>
        function printImage() {
            window.print(); // Lancer l'impression de la page
        }
    </script>
@endsection

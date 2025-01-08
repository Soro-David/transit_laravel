@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
    @csrf
    <section>
        <div class="container text-center">
            <h1>QR Code</h1>
            <img src="{{ asset($filePath) }}" alt="QR Code" style="max-width: 300px; margin: 0 auto; display: block;">
            <button class="btn btn-primary mt-4" onclick="window.print()">Imprimer</button>
        </div>
    </section>

    <style>
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

        img {
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        @media print {
            button {
                display: none; /* Masquer le bouton lors de l'impression */
            }

            section {
                box-shadow: none; /* Supprimer les ombres lors de l'impression */
            }
        }
    </style>
@endsection

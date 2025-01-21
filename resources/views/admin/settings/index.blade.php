@extends('admin.layouts.admin')

@section('content-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <ul class="horizontal-nav">
        <li><a href="{{route('setting.agence.index')}}">Agences</a></li>
        <li><a href="{{route('setting.chauffeur.index')}}">Chauffeurs</a></li>
    </ul>
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

        /* Effet au survol */
        .horizontal-nav li a:hover {
            background-color: white; /* Fond blanc au survol */
            color: black; /* Texte noir */
            /* Pas de text-decoration */
        }

        .horizontal-nav li a:active {
            background-color: #1abc9c; /* Couleur active */
        }
    </style>
@endsection

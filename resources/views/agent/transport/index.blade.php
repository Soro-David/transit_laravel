@extends('agent.layouts.agent')
@section('content-header')  
    <div class=" container">
        <nav class="flex space-x-4 justify-center">
            <!-- Bouton Voir Chauffeur -->
            <a href="{{route('transport.show.chauffeur')}}" 
            class="bg-white text-green-700 font-bold py-2 px-4 rounded hover:bg-green-600 hover:text-white">
                Voir Chauffeur
            </a>
            <a href="{{route('transport.list.chauffeur')}}" 
            class="bg-white text-green-700 font-bold py-2 px-4 rounded hover:bg-green-600 hover:text-white">
                Liste des Chauffeurs
            </a>
        </nav>
    </div>
@endsection
@section('content')
<section>
   <!-- sous-menu.blade.php -->


    <div class="container">
       
        <!-- sous-menu.blade.php -->
        
 
    </div>    
</section>      
@endsection

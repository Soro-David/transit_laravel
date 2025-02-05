@extends('admin.layouts.admin')

@section('content-header', 'Tableau de Bord')

@section('content')
<section class="py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-center">Ajouter un Gestionnaire</h2>
                        </div>
                        <div class="card-body">
                            <form action="{{route('managers.store')}}" method="post">
                                @csrf
                                @method('POST')
                                <div class="form-group">
                                    <label for="nom">Nom:</label>
                                    <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" id="nom" >
                                    @error('nom')
                                        <div class="text-danger">
                                            <p>{{$message}}</p>
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Prénom:</label>
                                    <input type="text" name="prenom" value="{{ old('prenom') }}" class="form-control" id="prenom" >
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail:</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="email" >
                                    @error('email')
                                    <div class="text-danger">
                                        <p>{{$message}}</p>
                                    </div>
                                @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Mot de passe:</label>
                                    <input type="password" name="password" class="form-control" id="password" >
                                </div>
                                <div class="form-group">
                                    <label for="role">Rôle:</label>
                                    <select name="role" value="{{ old('role') }}" class="form-control" id="role" >
                                        <option value="provider">Agent</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

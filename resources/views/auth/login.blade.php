@extends('admin.layouts.auth')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-12">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Connectez-vous</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="email">Adresse email</label>
                            <div class="input-group">
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                            </div>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Mot de passe" required autocomplete="current-password">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="form-check-label">Se souvenir</label>
                            </div>
                            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0">
                            <a href="{{ route('register') }}">Créer un compte</a>
                        </p>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    body {
    background-image: url('{{ asset('images/login2.png') }}'); /* Chemin de l'image de fond */
    background-size: auto; /* L'image garde sa taille d'origine */
    background-position: center; /* L'image reste centrée */
    background-repeat: no-repeat; /* L'image ne se répète pas */
    height: 100vh; /* Hauteur pleine de la fenêtre */
    display: flex;
    justify-content: center;
    align-items: center; /* Centre le formulaire au milieu de l'écran */
}

.card-body {
    
    width: 100%;
    max-width: 100%; /* Limite la largeur du formulaire */
    min-width: 100%;
   
}

.container {
    padding: 10px;
    width: 100%;
}

.card-header {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    background-color: #007bff; /* Couleur de fond du header */
    color: white;
}

.invalid-feedback {
    display: block;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-check-label {
    font-size: 0.875rem;
}

.form-control {
    border-radius: 0.25rem; /* Arrondir les bords des champs de texte */
}

@media (min-width: 1200px) {
    .card {
        max-width: 800px; /* Peut augmenter la taille du formulaire sur des écrans plus larges */
    }
}

</style>
@endsection

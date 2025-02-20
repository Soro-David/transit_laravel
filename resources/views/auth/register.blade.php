@extends('admin.layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Créer un compte</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Nom</label>
                                    <input id="last_name" type="text" 
                                           class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                           name="last_name" value="{{ old('last_name') }}" 
                                           required autocomplete="last_name">
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">Prénom</label>
                                    <input id="first_name" type="text" 
                                           class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                           name="first_name" value="{{ old('first_name') }}" 
                                           required autocomplete="first_name" autofocus>
                                    @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input id="email" type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" 
                                           required autocomplete="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tel">Téléphone</label>
                                    <input id="tel" type="tel" 
                                           class="form-control form-control-lg @error('tel') is-invalid @enderror"
                                           name="tel" value="{{ old('tel') }}" 
                                           required autocomplete="tel" autofocus>
                                    @error('tel')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adresse">Adresse</label>
                                    <input id="adresse" type="text" 
                                           class="form-control form-control-lg @error('adresse') is-invalid @enderror"
                                           name="adresse" value="{{ old('adresse') }}" 
                                           required autocomplete="adresse" autofocus>
                                    @error('adresse')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Mot de passe</label>
                                    <input id="password" type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="new-password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password-confirm">Confirmer le mot de passe</label>
                                    <input id="password-confirm" type="password" 
                                           class="form-control form-control-lg"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                S'inscrire
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            <a href="{{ route('login') }}">J'ai un compte</a>
                        </p>
                    </div>
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
    body {
        background-image: url('{{ asset('images/font_login1.jpg') }}'); /* Chemin de l'image de fond */
    background-size: auto; /* L'image garde sa taille d'origine */
    background-position: center; /* L'image reste centrée */
    background-repeat: no-repeat; /* L'image ne se répète pas */
    height: 100vh; /* Hauteur pleine de la fenêtre */
    display: flex;
    justify-content: center;
    align-items: center; 
}
.card-body{
    width: 100%;
    max-width: 600px; /
}

.card {
    margin: 2rem auto;
    border-radius: 15px;
    overflow: hidden;
    width: 100%;
    max-width: 600px; /* Limite la largeur du formulaire */
    background-color: rgba(255, 255, 255, 0.8); /* Arrière-plan semi-transparent pour contraster avec l'image */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Ombre subtile pour le formulaire */
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
        max-width: 1200px; /* Peut augmenter la taille du formulaire sur des écrans plus larges */
    }
}

</style>
@endsection

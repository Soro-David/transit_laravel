@extends('admin.layouts.auth')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-12">
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
    @media (min-width: 1200px) {
        .card {
            max-width: 700px;
            margin: 0 auto;
        }
    }

    .invalid-feedback {
        display: block;
    }
</style>
@endsection

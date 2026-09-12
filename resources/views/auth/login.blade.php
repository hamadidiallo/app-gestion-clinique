<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - CLINGEST</title>

    {{-- Bootstrap 5 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0d6efd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 1.5rem 0;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #0d6efd;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
    </style>
  </head>
  <body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center">
                <div class="card login-card border-0 bg-white">
                    <div class="card-header card-header-custom text-white text-center py-4 border-0">
                        <i class="bi bi-hospital fs-1 d-block mb-2"></i>
                        <h3 class="fw-bold mb-1">CLINGEST</h3>
                        <p class="mb-0 small opacity-75">Gestion Clinique Médicale</p>
                    </div>

                    <div class="card-body p-4 p-sm-5">
                        {{-- Messages d'alerte et erreurs d'authentification --}}
                        <x-form.erreur />
                        <x-form.alert />

                        <h5 class="fw-bold text-dark mb-4 text-center">Connexion à votre espace</h5>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- Champ Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-muted small">Adresse Email :</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus 
                                           placeholder="votre.email@clinique.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Champ Mot de passe --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-muted small">Mot de passe :</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           required 
                                           placeholder="••••••••">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Se souvenir de moi --}}
                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label text-muted small" for="remember">Se souvenir de moi sur cet appareil</label>
                            </div>

                            {{-- Bouton de Connexion --}}
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Se Connecter
                            </button>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3 border-0">
                        <span class="text-muted small">Vous n'avez pas de compte ?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none ms-1 small">S'inscrire ici</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

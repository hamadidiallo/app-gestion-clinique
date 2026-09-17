<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - CLINGEST</title>

    {{-- Google Fonts : IBM Plex Sans & IBM Plex Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --primary-color: #0f766e;
            --primary-hover: #0b5a54;
            --primary-soft: #e2f1ef;
        }

        body {
            background: #eef1f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 1.5rem 0;
            color: #1e2a32;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.08), 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e6ebf0;
            overflow: hidden;
            background: #ffffff;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .lucide {
            width: 1.15rem;
            height: 1.15rem;
            stroke-width: 1.9;
            vertical-align: -0.15em;
            display: inline-block;
        }
    </style>
  </head>
  <body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center">
                <div class="card login-card">
                    <div class="p-4 text-center border-bottom bg-white">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2" style="width: 48px; height: 48px; background: #0f766e; color: #ffffff;">
                            <i data-lucide="cross" style="width: 24px; height: 24px;"></i>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">CLINGEST</h3>
                        <p class="mb-0 small text-muted">Clinique Gahambani · Bamako / Kati</p>
                    </div>

                    <div class="card-body p-4 p-sm-5">
                        {{-- Messages d'alerte et erreurs d'authentification --}}
                        <x-form.erreur />
                        <x-form.alert />

                        <div class="mb-4 text-center">
                            <h5 class="fw-bold text-dark mb-1">Espace Professionnel</h5>
                            <p class="small text-muted mb-0">Identifiez-vous pour accéder à la caisse et aux dossiers</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- Champ Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-muted small">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i data-lucide="mail" class="lucide-sm"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus 
                                           placeholder="agent@clingest.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Champ Mot de passe --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-muted small">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i data-lucide="lock" class="lucide-sm"></i>
                                    </span>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror" 
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
                                <label class="form-check-label text-muted small" for="remember">Mémoriser ma session</label>
                            </div>

                            {{-- Bouton de Connexion --}}
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-3 d-inline-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="log-in" class="lucide-sm"></i>
                                <span>Se Connecter</span>
                            </button>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3 border-top">
                        <span class="text-muted small">Besoin d'accès ? Contactez l'administrateur ou</span>
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1 small" style="color: #0f766e;">Créer un compte</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
  </body>
</html>

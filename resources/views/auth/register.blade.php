<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un Compte - CLINGEST</title>

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

        .register-card {
            width: 100%;
            max-width: 560px;
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
                <div class="card register-card border-0 bg-white">
                    <div class="card-header card-header-custom text-white text-center py-4 border-0">
                        <i data-lucide="user-plus" class="fs-1 d-block mb-2"></i>
                        <h3 class="fw-bold mb-1">Créer un Compte</h3>
                        <p class="mb-0 small opacity-75">CLINGEST - Gestion Clinique Médicale</p>
                    </div>

                    <div class="card-body p-4 p-sm-5">
                        {{-- Messages d'alerte --}}
                        <x-form.erreur />
                        <x-form.alert />

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row g-3">
                                {{-- Nom --}}
                                <div class="col-md-6">
                                    <label for="nom" class="form-label fw-semibold text-muted small">Nom de Famille <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required placeholder="Ex: NIKIZA">
                                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Prénom --}}
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label fw-semibold text-muted small">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required placeholder="Ex: Jean">
                                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-12">
                                    <label for="email" class="form-label fw-semibold text-muted small">Adresse Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="jean.nikiza@clinique.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Rôle --}}
                                <div class="col-md-12">
                                    <label for="role_id" class="form-label fw-semibold text-muted small">Rôle / Fonction <span class="text-danger">*</span></label>
                                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                        <option value="">-- Choisir un rôle --</option>
                                        @foreach($roles as $id => $nom)
                                            <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Mot de passe --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold text-muted small">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Confirmation mot de passe --}}
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold text-muted small">Confirmer mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm rounded-3 mt-4">
                                <i data-lucide="check-circle" class="me-2"></i> Finaliser l'Inscription
                            </button>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3 border-0">
                        <span class="text-muted small">Vous avez déjà un compte ?</span>
                        <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none ms-1 small">Se connecter ici</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

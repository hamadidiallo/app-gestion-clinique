@extends('layout')

@section('title', 'Modifier l\'Utilisateur - CLINGEST')

@section('content')
<div class="container-fluid p-0" style="max-width: 900px;">
    {{-- Fil d'Ariane & En-tête --}}
    <div class="mb-4">
        <div class="small text-muted mb-1 d-flex align-items-center gap-1">
            <a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Utilisateurs</a>
            <span class="opacity-50">/</span>
            <span class="fw-semibold text-dark">Modification #{{ $user->id }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; background: #e2f1ef; color: #0f766e; font-size: 1.1rem;">
                    {{ strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->nom ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $user->prenom }} {{ $user->nom }}</h1>
                    <p class="text-muted small mb-0">{{ $user->email }} · {{ $user->role->nom ?? 'Aucun rôle' }}</p>
                </div>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" class="lucide-sm"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" class="card shadow-sm border-0">
        @csrf
        @method('PUT')

        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7; color: #b45309;">
                <i data-lucide="edit-2" class="lucide-sm"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold text-dark">Modifier les Identifiants & Profil</h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- Prénom --}}
                <div class="col-md-6">
                    <label for="prenom" class="form-label fw-semibold text-dark">
                        Prénom <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="user" class="lucide-sm text-muted"></i></span>
                        <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $user->prenom) }}" required>
                    </div>
                    @error('prenom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Nom --}}
                <div class="col-md-6">
                    <label for="nom" class="form-label fw-semibold text-dark">
                        Nom de famille <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="user" class="lucide-sm text-muted"></i></span>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $user->nom) }}" required>
                    </div>
                    @error('nom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="telephone" class="form-label fw-semibold text-dark">
                        Téléphone (Identifiant de connexion)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="phone" class="lucide-sm text-muted"></i></span>
                        <input type="text" name="telephone" id="telephone" inputmode="tel" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $user->telephone) }}" placeholder="Ex: 76 00 00 00" required>
                    </div>
                    @error('telephone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold text-dark">
                        Adresse Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="mail" class="lucide-sm text-muted"></i></span>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    </div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <div class="form-text text-muted small">Téléphone ou e-mail : renseignez au moins l'un des deux, il servira à se connecter.</div>
                </div>

                {{-- Rôle --}}
                <div class="col-md-6">
                    <label for="role_id" class="form-label fw-semibold text-dark">
                        Rôle / Profil d'accès (RBAC) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="shield-check" class="lucide-sm text-muted"></i></span>
                        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            @foreach($roles as $id => $nom)
                                <option value="{{ $id }}" {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
                                    {{ $nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Mot de passe (Optionnel en modification) --}}
                <div class="col-md-12">
                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <label for="password" class="form-label fw-bold text-dark mb-1">
                            Modifier le mot de passe
                        </label>
                        <p class="text-muted small mb-2">Laissez ce champ vide si vous souhaitez conserver le mot de passe existant de l'utilisateur.</p>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i data-lucide="lock" class="lucide-sm text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control font-mono @error('password') is-invalid @enderror" placeholder="Laisser vide pour ne pas changer">
                        </div>
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Dernière modification enregistrée le {{ $user->updated_at ? $user->updated_at->format('d/m/Y à H:i') : '—' }}
            </span>
            <button type="submit" class="btn btn-warning d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold">
                <i data-lucide="save" class="lucide-sm"></i>
                <span>Enregistrer les Modifications</span>
            </button>
        </div>
    </form>
</div>
@endsection

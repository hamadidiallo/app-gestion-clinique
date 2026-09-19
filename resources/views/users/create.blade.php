@extends('layout')

@section('title', 'Créer un Utilisateur - CLINGEST')

@section('content')
<div class="container-fluid p-0" style="max-width: 900px;">
    {{-- Fil d'Ariane & En-tête --}}
    <div class="mb-4">
        <div class="small text-muted mb-1 d-flex align-items-center gap-1">
            <a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Utilisateurs</a>
            <span class="opacity-50">/</span>
            <span class="fw-semibold text-dark">Nouveau Compte</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Créer un Nouvel Utilisateur</h1>
                <p class="text-muted small mb-0">Définissez les identifiants d'accès et attribuez le rôle fonctionnel approprié.</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" class="lucide-sm"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="card shadow-sm border-0">
        @csrf

        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #ede9fe; color: #6d28d9;">
                <i data-lucide="user-plus" class="lucide-sm"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold text-dark">Informations du Compte & Droits d'Accès</h5>
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
                        <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" placeholder="Ex: Fatou" required autofocus>
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
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" placeholder="Ex: Traoré" required>
                    </div>
                    @error('nom')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold text-dark">
                        Adresse Email (Identifiant de connexion) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="mail" class="lucide-sm text-muted"></i></span>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Ex: caissier@clinique.local" required>
                    </div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Rôle --}}
                <div class="col-md-6">
                    <label for="role_id" class="form-label fw-semibold text-dark">
                        Rôle / Profil d'accès (RBAC) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="shield-check" class="lucide-sm text-muted"></i></span>
                        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner un profil --</option>
                            @foreach($roles as $id => $nom)
                                <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                    {{ $nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Mot de passe --}}
                <div class="col-md-12">
                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <label for="password" class="form-label fw-bold text-dark mb-1">
                            Mot de passe initial <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Définissez un mot de passe sécurisé pour ce compte (minimum 6 caractères).</p>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i data-lucide="lock" class="lucide-sm text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control font-mono @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        </div>
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i data-lucide="info" class="lucide-sm me-1 text-primary"></i>
                L'utilisateur héritera automatiquement des autorisations et menus du rôle sélectionné.
            </span>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                <i data-lucide="check-circle" class="lucide-sm"></i>
                <span class="fw-semibold">Enregistrer le Compte</span>
            </button>
        </div>
    </form>
</div>
@endsection

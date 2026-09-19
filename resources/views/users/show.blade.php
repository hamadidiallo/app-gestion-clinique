@extends('layout')

@section('title', 'Fiche Utilisateur #' . $user->id . ' - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane & En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Utilisateurs</a>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Fiche Compte #{{ $user->id }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; background: #e2f1ef; color: #0f766e; font-size: 1.2rem;">
                    {{ strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->nom ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $user->prenom }} {{ $user->nom }}</h1>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="text-muted small font-mono">{{ $user->email }}</span>
                        <span class="badge badge-info-pill">{{ $user->role->nom ?? 'Aucun rôle' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-2">
                <i data-lucide="edit-2" class="lucide-sm"></i>
                <span>Modifier</span>
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" class="lucide-sm"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    {{-- Contenu en grille --}}
    <div class="row g-4">
        {{-- Colonne Gauche : Données du compte --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="user" class="lucide-sm text-primary" style="color: var(--primary-color) !important;"></i>
                    <h5 class="card-title mb-0 fw-bold text-dark">Détails du Compte</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Identifiant Système :</span>
                        <span class="font-mono fw-bold text-dark">#{{ $user->id }}</span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Nom complet :</span>
                        <span class="fw-bold text-dark">{{ $user->prenom }} {{ $user->nom }}</span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Adresse Email :</span>
                        <span class="font-mono text-dark">{{ $user->email }}</span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Profil / Rôle Attribué :</span>
                        <span class="badge badge-success-pill">{{ $user->role->nom ?? 'Aucun rôle' }}</span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Date de Création :</span>
                        <span class="font-mono text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Dernière Modification :</span>
                        <span class="font-mono text-dark">{{ $user->updated_at ? $user->updated_at->format('d/m/Y à H:i') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne Droite : Privilèges RBAC & Rôle --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="shield" class="lucide-sm text-primary" style="color: var(--primary-color) !important;"></i>
                    <h5 class="card-title mb-0 fw-bold text-dark">Périmètre d'Accès & Sécurité</h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <h6 class="fw-bold text-dark mb-1">
                            Rôle Actif : {{ $user->role->nom ?? 'Sans rôle' }}
                        </h6>
                        <p class="text-muted small mb-0">
                            {{ $user->role->description ?? 'Aucune description disponible pour ce rôle.' }}
                        </p>
                    </div>

                    <h6 class="fw-bold text-dark small text-uppercase mb-3">Modules Autorisés</h6>
                    <ul class="list-unstyled mb-0">
                        @if($user->isAdmin())
                            <li class="d-flex align-items-center gap-2 mb-2 text-success small">
                                <i data-lucide="check-circle-2" class="lucide-sm"></i>
                                <span><strong>Accès Super-Administrateur :</strong> Tous les modules et réglages</span>
                            </li>
                        @else
                            <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                <i data-lucide="check" class="lucide-xs text-success"></i>
                                <span>Tableau de bord personnalisé</span>
                            </li>
                            @if($user->hasRole(['Médecin']))
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Dossier médical, consultations et suivi des prestations</span>
                                </li>
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Suivi de ses rémunérations et honoraires</span>
                                </li>
                            @endif
                            @if($user->hasRole(['Caissier']))
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Ouverture/fermeture de session de caisse et Ticket Z</span>
                                </li>
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Encaissement des tickets et règlements de dettes</span>
                                </li>
                            @endif
                            @if($user->hasRole(['Réceptionniste']))
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Accueil, création de dossiers patients et émission des tickets</span>
                                </li>
                            @endif
                            @if($user->hasRole(['Comptable']))
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Gestion comptable : Recettes, Dépenses et Rapprochement de caisses</span>
                                </li>
                                <li class="d-flex align-items-center gap-2 mb-2 small text-dark">
                                    <i data-lucide="check" class="lucide-xs text-success"></i>
                                    <span>Calcul des paies et rémunérations des praticiens</span>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

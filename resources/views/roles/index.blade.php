@extends('layout')

@section('title', 'Gestion des Rôles & Droits d\'Accès - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="text-muted small">Administration &amp; Sécurité</span>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small font-mono">Contrôle d'Accès (RBAC)</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="shield-check" class="lucide"></i>
                </div>
                <span>Gestion des Rôles &amp; Permissions</span>
            </h1>
            <p class="text-muted mb-0 small">
                Configuration des profils d'utilisateurs, périmètres de sécurité et permissions métier au sein de la clinique.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 no-print">
            <x-export-buttons table-id="rolesTable" title="Liste_des_Roles" filename="roles_acces_clinique" />
            <a href="{{ route('role.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="plus" class="lucide-sm"></i>
                <span>Nouveau Rôle</span>
            </a>
        </div>
    </div>

    {{-- Synthèse RBAC (Cartes KPIs Modernes) --}}
    <div class="row g-3 mb-4">
        {{-- Total Rôles --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Rôles Définis</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="shield" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark font-mono">
                    {{ $totalRoles }} <small class="fs-6 text-muted">profil(s)</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Profils configurés dans le système
                </p>
            </div>
        </div>

        {{-- Total Utilisateurs Assignés --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Utilisateurs Actifs</span>
                    <div class="bg-info-subtle text-info p-2 rounded-circle">
                        <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark font-mono">
                    {{ $totalUsers }} <small class="fs-6 text-muted">compte(s)</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Comptes du personnel rattachés
                </p>
            </div>
        </div>

        {{-- Rôles Protégés --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Rôles Système</span>
                    <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark font-mono">
                    5 <small class="fs-6 text-muted">fondamentaux</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Rôles maîtres protégés (Admin, Soins, Caisse)
                </p>
            </div>
        </div>

        {{-- Sécurité RBAC --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Contrôle d'Accès</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success font-mono">
                    RBAC <small class="fs-6">Strict</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Isolation stricte par middleware de sécurité
                </p>
            </div>
        </div>
    </div>

    {{-- Tableau des Rôles --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="key-round" class="lucide text-primary"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    Répertoire des Profils &amp; Rôles
                </h5>
            </div>
            <span class="badge bg-light text-dark border font-mono small">
                {{ $roles->count() }} profil(s) configuré(s)
            </span>
        </div>

        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 60px;">#</th>
                        <th style="width: 220px;">Profil &amp; Intitulé</th>
                        <th>Description &amp; Périmètre d'Action</th>
                        <th class="text-center" style="width: 160px;">Utilisateurs</th>
                        <th class="text-center" style="width: 150px;">Niveau d'Intégrité</th>
                        <th style="width: 140px;">Date Création</th>
                        <th class="text-center pe-3" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        @php
                            $isProtected = in_array($role->nom, $protectedRoles);
                            // Détermination du style et de l'icône par rôle
                            $badgeClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                            $iconName = 'shield';

                            if ($role->nom === 'Administrateur') {
                                $badgeClass = 'bg-purple-subtle text-purple border-purple-subtle';
                                $iconName = 'shield-alert';
                            } elseif ($role->nom === 'Médecin') {
                                $badgeClass = 'bg-teal-subtle text-teal border-teal-subtle';
                                $iconName = 'stethoscope';
                            } elseif ($role->nom === 'Caissier') {
                                $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                $iconName = 'banknote';
                            } elseif ($role->nom === 'Réceptionniste') {
                                $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                                $iconName = 'user-check';
                            } elseif ($role->nom === 'Comptable') {
                                $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                $iconName = 'calculator';
                            }
                        @endphp
                        <tr>
                            <td class="ps-3 font-mono text-muted fw-semibold">{{ $role->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="background-color: #f1f5f9; width: 34px; height: 34px;">
                                        <i data-lucide="{{ $iconName }}" style="width: 18px; height: 18px;" class="text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block">{{ $role->nom }}</strong>
                                        <span class="badge {{ $badgeClass }} border small py-0 px-2 font-mono" style="font-size: 10px;">
                                            {{ strtoupper($role->nom) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small d-block" style="max-width: 480px; line-height: 1.4;">
                                    {{ $role->description ?: 'Aucune description spécifique renseignée pour ce rôle.' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('roles.show', $role) }}" class="text-decoration-none">
                                    <span class="badge {{ $role->users_count > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }} px-2 py-1 font-mono">
                                        <i data-lucide="users" style="width: 12px; height: 12px;" class="me-1"></i>
                                        {{ $role->users_count }} utilisateur{{ $role->users_count > 1 ? 's' : '' }}
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                @if($isProtected)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small" title="Ce rôle est indispensable au fonctionnement des modules de l'application">
                                        <i data-lucide="lock" style="width: 11px; height: 11px;" class="me-1"></i> Système Protégé
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 small">
                                        Personnalisé
                                    </span>
                                @endif
                            </td>
                            <td class="font-mono text-muted small">
                                {{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Bouton Voir --}}
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-primary p-1 px-2 d-inline-flex align-items-center gap-1" title="Voir les détails et utilisateurs">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-md-inline">Voir</span>
                                    </a>

                                    {{-- Bouton Modifier --}}
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary p-1 px-2 d-inline-flex align-items-center gap-1" title="Modifier le libellé ou la description">
                                        <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-md-inline">Modifier</span>
                                    </a>

                                    {{-- Bouton Supprimer --}}
                                    @if($isProtected)
                                        <button type="button" class="btn btn-sm btn-outline-secondary p-1 px-2 opacity-50" disabled title="Les rôles système fondamentaux ne peuvent pas être supprimés">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}" title="Supprimer ce rôle">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            <span class="d-none d-md-inline">Supprimer</span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Fenêtre Modale de Confirmation de Suppression --}}
                                @if(!$isProtected)
                                    <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start rounded-4 border-0 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-danger-subtle text-danger p-2 rounded-circle">
                                                            <i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i>
                                                        </div>
                                                        <h5 class="modal-title fw-bold text-dark" id="deleteRoleModalLabel{{ $role->id }}">
                                                            Supprimer le rôle
                                                        </h5>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <p class="mb-2">Êtes-vous sûr de vouloir supprimer définitivement le rôle <strong class="text-dark">{{ $role->nom }}</strong> ?</p>
                                                    @if($role->users_count > 0)
                                                        <div class="alert alert-danger py-2 small mb-0 d-flex align-items-center gap-2">
                                                            <i data-lucide="alert-circle" style="width: 18px; height: 18px;" class="flex-shrink-0"></i>
                                                            <span>Attention : <strong>{{ $role->users_count }} utilisateur(s)</strong> possède(nt) actuellement ce rôle.</span>
                                                        </div>
                                                    @else
                                                        <p class="text-muted small mb-0">Cette action est irréversible.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('roles.destroy', $role) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger fw-bold">Confirmer la suppression</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="p-4">
                                    <i data-lucide="shield-alert" class="lucide mb-2 text-muted" style="width: 48px; height: 48px;"></i>
                                    <p class="mb-1 fw-bold text-dark">Aucun rôle enregistré</p>
                                    <p class="small text-muted mb-0">Cliquez sur « Nouveau Rôle » pour créer votre premier profil de permissions.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection

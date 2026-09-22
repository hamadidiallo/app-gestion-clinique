@extends('layout')

@section('title', 'Détails du Rôle : ' . $role->nom . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page & Navigation --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('roles.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour aux Rôles
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small font-mono">Fiche Profil RBAC</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="shield-check" class="lucide"></i>
                </div>
                <span>Détails du Rôle : {{ $role->nom }}</span>
            </h1>
            <p class="text-muted mb-0 small">
                Consultez le périmètre, les permissions et la liste des collaborateurs disposant de ce rôle.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 no-print">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="edit-2" class="lucide-sm"></i>
                <span>Modifier le Rôle</span>
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="list" class="lucide-sm"></i>
                <span>Liste des Rôles</span>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Fiche descriptive du rôle --}}
        <div class="col-lg-5 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-4 p-3 d-inline-flex align-items-center justify-content-center text-white" style="background-color: #0f2b48; width: 56px; height: 56px;">
                        <i data-lucide="shield" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $role->nom }}</h4>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono small">
                            Identifiant #{{ $role->id }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small text-muted text-uppercase fw-bold d-block mb-1">Description &amp; Périmètre :</label>
                    <p class="text-dark bg-light p-3 rounded-3 mb-0" style="line-height: 1.5;">
                        {{ $role->description ?: 'Aucune description spécifique renseignée.' }}
                    </p>
                </div>

                <div class="row g-2 pt-2 border-top">
                    <div class="col-6">
                        <small class="text-muted d-block">Date de création :</small>
                        <strong class="font-mono text-dark">{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Dernière mise à jour :</small>
                        <strong class="font-mono text-dark">{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des utilisateurs rattachés --}}
        <div class="col-lg-7 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="users" class="lucide text-primary"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            Utilisateurs Assignés
                        </h5>
                    </div>
                    <span class="badge bg-light text-dark border font-mono small">
                        {{ $role->users->count() }} collaborateur(s)
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th>Utilisateur</th>
                                <th>Email Professionnel</th>
                                <th class="text-end pe-3" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($role->users as $user)
                                <tr>
                                    <td class="ps-3 font-mono text-muted small">{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 32px; height: 32px;">
                                                {{ substr($user->prenom ?? 'U', 0, 1) }}{{ substr($user->nom ?? '', 0, 1) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark d-block">{{ $user->prenom }} {{ $user->nom }}</strong>
                                                <small class="text-muted font-mono">{{ $user->reference ?? ('USR-' . str_pad($user->id, 4, '0', STR_PAD_LEFT)) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono small text-muted">
                                        {{ $user->email }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary p-1 px-2 d-inline-flex align-items-center gap-1" title="Voir la fiche utilisateur">
                                            <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                            <span>Fiche</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i data-lucide="user-x" class="lucide mb-2 text-muted" style="width: 36px; height: 36px;"></i>
                                        <p class="mb-0 small">Aucun collaborateur n'est actuellement affecté à ce rôle.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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

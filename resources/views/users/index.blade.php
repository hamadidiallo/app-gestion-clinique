@extends('layout')

@section('title', 'Gestion des Utilisateurs & Comptes - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec titre, fil d'Ariane et actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Administration</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Sécurité & Accès</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">Gestion des Utilisateurs</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="key-round" class="lucide-sm"></i>
                <span>Gérer les Rôles</span>
            </a>
            <a href="{{ route('user.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i data-lucide="user-plus" class="lucide-sm"></i>
                <span class="fw-semibold">Créer un Utilisateur</span>
            </a>
        </div>
    </div>

    {{-- Cartes d'indicateurs (KPIs) --}}
    <div class="row g-3 mb-4">
        {{-- Total Utilisateurs --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Total Comptes</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="users" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $stats['total'] }}</div>
                <div class="small text-muted">Comptes actifs sur le système</div>
            </div>
        </div>

        {{-- Administrateurs --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Administrateurs</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #ede9fe; color: #6d28d9;">
                        <i data-lucide="shield-check" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $stats['admins'] }}</div>
                <div class="small text-muted">Accès complet et supervision</div>
            </div>
        </div>

        {{-- Corps Médical --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Corps Médical</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #e0f2fe; color: #0284c7;">
                        <i data-lucide="stethoscope" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $stats['medecins'] }}</div>
                <div class="small text-muted">Médecins & Praticiens</div>
            </div>
        </div>

        {{-- Caisse, Réception & Compta --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Caisse & Finance</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="wallet" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $stats['caisse_accueil'] + $stats['comptables'] }}</div>
                <div class="small text-muted">Caissiers, Accueil & Comptables</div>
            </div>
        </div>
    </div>

    {{-- Bannière Code d'Invitation de la Clinique --}}
    @if(auth()->user()->clinique)
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); color: #ffffff; border-radius: 12px;">
        <div class="card-body p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.15);">
                    <i data-lucide="key" style="width: 24px; height: 24px; color: #5eead4;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white text-dark fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Code d'invitation Clinique</span>
                        <span class="text-white-50 small">• {{ auth()->user()->clinique->nom }}</span>
                    </div>
                    <div class="small text-white-50" style="max-width: 580px;">
                        Transmettez un code d'invitation à vos collaborateurs. Vous pouvez utiliser le code général de l'établissement ou générer une invitation sécurisée avec un rôle pré-assigné (Médecin, Caissier, etc.).
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 bg-white rounded-3 p-2 px-3 text-dark shadow-sm">
                    <span class="text-muted small me-1">Général :</span>
                    <span class="font-mono fs-5 fw-bold letter-spacing-1 text-teal" id="invitationCodeText">{{ auth()->user()->clinique->code_invitation ?? 'NON-DÉFINI' }}</span>
                    <button type="button" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 ms-1" onclick="copyInvitationCode()" id="copyCodeBtn" title="Copier le code">
                        <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                        <span id="copyText" class="fw-semibold">Copier</span>
                    </button>
                </div>
                <button type="button" class="btn btn-light fw-bold text-teal d-inline-flex align-items-center gap-1 shadow-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalNouvelleInvitation">
                    <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                    <span>+ Invitation avec rôle fixé</span>
                </button>
            </div>
        </div>

        {{-- Tableau des invitations spécifiques en attente (Option 1) --}}
        @if(isset($invitations) && $invitations->isNotEmpty())
        <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-success-subtle text-success p-2 d-inline-flex">
                        <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Invitations nominatives en attente (Option 1 Sécurisée)</h6>
                        <small class="text-muted">Ces codes verrouillent le rôle du soignant lors de son inscription sans qu'il puisse le modifier.</small>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                    {{ $invitations->count() }} en attente
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-4">Code d'accès</th>
                            <th>Rôle pré-attribué</th>
                            <th>Destinataire</th>
                            <th>Créée le</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $inv)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark font-mono px-3 py-2 fs-6 letter-spacing-1">{{ $inv->code }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary p-1" onclick="copyCustomCode('{{ $inv->code }}', this)" title="Copier le code">
                                        <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                                    {{ $inv->role->nom ?? 'Non défini' }}
                                </span>
                            </td>
                            <td>
                                @if($inv->prenom || $inv->nom)
                                    <strong class="text-dark">{{ $inv->prenom }} {{ $inv->nom }}</strong>
                                    @if($inv->email)
                                        <div class="text-muted small">{{ $inv->email }}</div>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Tous collaborateurs pour ce rôle</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $inv->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('users.invitations.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Révoquer ce code d\'invitation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        <span>Révoquer</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Barre de filtres et recherche --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i data-lucide="search" class="lucide-sm text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher par nom, prénom ou email..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="role_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les rôles ({{ $stats['total'] }})</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>
                            {{ $role->nom }} ({{ $role->users_count }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                    <i data-lucide="filter" class="lucide-sm"></i>
                    <span>Filtrer</span>
                </button>
                @if($search || $roleId)
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i data-lucide="rotate-ccw" class="lucide-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau des utilisateurs --}}
    <div class="card overflow-hidden shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="user-cog" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Annuaire des Utilisateurs</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="usersTable" title="Liste des Utilisateurs" filename="utilisateurs" />
                <span class="badge bg-light text-muted border font-mono">{{ $users->count() }} compte(s)</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 70px;">ID</th>
                            <th>Utilisateur</th>
                            <th>Email de Connexion</th>
                            <th>Rôle / Permissions</th>
                            <th>Date de Création</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $roleNom = $user->role->nom ?? 'Sans rôle';
                                $roleLower = mb_strtolower($roleNom);

                                if (str_contains($roleLower, 'admin')) {
                                    $avatarBg = '#ede9fe';
                                    $avatarColor = '#6d28d9';
                                    $badgeClass = 'badge-info-pill';
                                    $badgeIcon = 'shield';
                                } elseif (str_contains($roleLower, 'medecin') || str_contains($roleLower, 'médecin')) {
                                    $avatarBg = '#e0f2fe';
                                    $avatarColor = '#0369a1';
                                    $badgeClass = 'badge-info-pill';
                                    $badgeIcon = 'stethoscope';
                                } elseif (str_contains($roleLower, 'caissier')) {
                                    $avatarBg = '#e3f3ee';
                                    $avatarColor = '#0f6b5f';
                                    $badgeClass = 'badge-success-pill';
                                    $badgeIcon = 'banknote';
                                } elseif (str_contains($roleLower, 'reception') || str_contains($roleLower, 'réception')) {
                                    $avatarBg = '#e2f1ef';
                                    $avatarColor = '#0f766e';
                                    $badgeClass = 'badge-info-pill';
                                    $badgeIcon = 'user-check';
                                } elseif (str_contains($roleLower, 'comptable')) {
                                    $avatarBg = '#fdf0d5';
                                    $avatarColor = '#8a5712';
                                    $badgeClass = 'badge-warning-pill';
                                    $badgeIcon = 'calculator';
                                } else {
                                    $avatarBg = '#f1f5f9';
                                    $avatarColor = '#475569';
                                    $badgeClass = 'bg-light text-muted border';
                                    $badgeIcon = 'user';
                                }
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <span class="font-mono text-muted small">#{{ $user->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background: {{ $avatarBg }}; color: {{ $avatarColor }}; font-size: 0.8rem;">
                                            {{ strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->nom ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->prenom }} {{ $user->nom }}</div>
                                            @if(auth()->id() === $user->id)
                                                <small class="badge bg-success-subtle text-success border border-success-subtle py-0 px-1" style="font-size: 0.65rem;">(Vous)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono small text-dark">{{ $user->email }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }} d-inline-flex align-items-center gap-1">
                                        <i data-lucide="{{ $badgeIcon }}" class="lucide-xs"></i>
                                        <span>{{ $roleNom }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-mono text-muted small">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary" title="Consulter la fiche">
                                            <i data-lucide="eye" class="lucide-sm"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i data-lucide="edit-2" class="lucide-sm"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}" title="Supprimer">
                                                <i data-lucide="trash-2" class="lucide-sm"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Modale de suppression --}}
                                    @if(auth()->id() !== $user->id)
                                    <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white py-2">
                                                    <h6 class="modal-title d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" class="lucide-sm"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start py-4">
                                                    Êtes-vous sûr de vouloir supprimer définitivement le compte de <strong>{{ $user->prenom }} {{ $user->nom }}</strong> ({{ $user->email }}) ?
                                                    <div class="alert alert-warning mt-3 mb-0 small">
                                                        Cette action est irréversible et révoquera immédiatement tous les accès associés.
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer définitivement</button>
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i data-lucide="users" class="lucide-lg d-block mx-auto mb-2 opacity-50"></i>
                                    Aucun utilisateur ne correspond à vos critères de recherche.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal : Générer une invitation sécurisée (Option 1) --}}
<div class="modal fade" id="modalNouvelleInvitation" tabindex="-1" aria-labelledby="modalNouvelleInvitationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-teal text-white p-2 d-inline-flex" style="background: #047857;">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalNouvelleInvitationLabel">Nouvelle invitation sécurisée</h5>
                        <small class="text-muted">Rôle verrouillé par l'administration (Option 1)</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('users.invitations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Notice explicative --}}
                    <div class="alert alert-success d-flex align-items-start gap-2 py-2 px-3 mb-3 border-0 rounded-3" style="background: #F0FDF9; color: #065F46;">
                        <i data-lucide="info" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 2px;"></i>
                        <div class="small">
                            Un code d'accès unique à <strong>6 caractères</strong> sera généré. Le rôle choisi ci-dessous sera <strong>verrouillé</strong> sans que le collaborateur ne puisse le changer.
                        </div>
                    </div>

                    {{-- Sélection du rôle obligatoire --}}
                    <div class="mb-3">
                        <label for="invitation_role_id" class="form-label fw-bold small text-dark">
                            Rôle attribué au collaborateur <span class="text-danger">*</span>
                        </label>
                        <select name="role_id" id="invitation_role_id" class="form-select form-select-lg fw-semibold" required>
                            <option value="">Sélectionnez un rôle officiel...</option>
                            @foreach($assignableRoles ?? [] as $role)
                                <option value="{{ $role->id }}">{{ $role->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nom & Prénom optionnels --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="invitation_prenom" class="form-label fw-semibold small text-muted">Prénom</label>
                            <input type="text" name="prenom" id="invitation_prenom" class="form-control" placeholder="Ex: Aminata">
                        </div>
                        <div class="col-6">
                            <label for="invitation_nom" class="form-label fw-semibold small text-muted">Nom</label>
                            <input type="text" name="nom" id="invitation_nom" class="form-control text-uppercase" placeholder="Ex: TRAORÉ">
                        </div>
                    </div>

                    {{-- Email optionnel --}}
                    <div class="mb-2">
                        <label for="invitation_email" class="form-label fw-semibold small text-muted">Adresse e-mail (Optionnel)</label>
                        <input type="email" name="email" id="invitation_email" class="form-control" placeholder="ex: a.traore@clinique.ml">
                        <div class="form-text extra-small">Si renseigné, ces informations seront pré-remplies automatiquement sur son écran de confirmation.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3 px-4">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn text-white fw-bold px-4 d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #10B981, #047857);">
                        <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                        <span>Générer le code d'accès</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyInvitationCode() {
        const codeElement = document.getElementById('invitationCodeText');
        if (!codeElement) return;
        const code = codeElement.innerText.trim();
        
        navigator.clipboard.writeText(code).then(() => {
            const copyText = document.getElementById('copyText');
            if (copyText) {
                const originalText = copyText.innerText;
                copyText.innerText = 'Copié !';
                setTimeout(() => {
                    copyText.innerText = originalText;
                }, 2000);
            }
        }).catch(err => {
            console.error('Erreur lors de la copie :', err);
        });
    }

    function copyCustomCode(code, btn) {
        if (!code) return;
        navigator.clipboard.writeText(code).then(() => {
            if (btn) {
                const oldHtml = btn.innerHTML;
                btn.innerHTML = '<span class="text-success fw-bold" style="font-size: 11px;">Copié !</span>';
                setTimeout(() => {
                    btn.innerHTML = oldHtml;
                }, 2000);
            }
        }).catch(err => {
            console.error('Erreur lors de la copie :', err);
        });
    }
</script>
@endpush


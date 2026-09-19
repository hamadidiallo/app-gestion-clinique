@extends('layout')

@section('title', 'Ouvrir une Session de Caisse - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane & En-tête Hero Moderne --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0f766e, #134e4a); color: #ffffff;">
                <i data-lucide="wallet" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                    <a href="{{ route('caisses.index') }}" class="text-decoration-none text-muted">Sessions de Caisse</a>
                    <span class="opacity-50">/</span>
                    <span class="fw-semibold text-dark">Nouvelle Vacation</span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-0">Ouverture de Session de Caisse</h1>
            </div>
        </div>
        <a href="{{ route('caisses.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i data-lucide="arrow-left" class="lucide-sm"></i>
            <span>Retour aux sessions</span>
        </a>
    </div>

    <form action="{{ route('caisses.store') }}" method="POST" id="caisseOpenForm">
        @csrf
        <input type="hidden" name="statut" value="ouverte">

        <div class="row g-4">
            {{-- Colonne Principale (8 cols) : Formulaire et paramètres --}}
            <div class="col-lg-8">
                {{-- Carte 1 : Opérateur & Horodatage --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #e2f1ef; color: #0f766e;">
                                <i data-lucide="user-check" class="lucide-sm"></i>
                            </span>
                            <h6 class="card-title mb-0 fw-bold text-dark">Opérateur & Prise de Poste</h6>
                        </div>
                        <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #12a594;"></span>
                            <span>Prêt pour vacation</span>
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Caissier Responsable --}}
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark mb-1">
                                    Caissier(ère) Responsable <span class="text-danger">*</span>
                                </label>
                                @if(!empty($isCaissier))
                                    <div class="p-3 rounded-3 border bg-light d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: #0f766e; font-size: 1rem;">
                                            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->nom ?? 'C', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="fw-bold text-dark fs-6 text-truncate">{{ auth()->user()->name ?? auth()->user()->nom }}</div>
                                            <div class="text-muted small d-flex align-items-center gap-2">
                                                <span class="badge badge-info-pill font-mono fw-semibold" style="background-color: #e2f1ef; color: #0f766e; border: 1px solid #b2ddd6;">
                                                    {{ auth()->user()->role->nom ?? 'Caissier' }}
                                                </span>
                                                <span>·</span>
                                                <span class="d-inline-flex align-items-center gap-1 text-success fw-medium">
                                                    <i data-lucide="shield-check" style="width: 13px; height: 13px;"></i>
                                                    <span>Session vérifiée</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i data-lucide="user" class="lucide-sm text-muted"></i></span>
                                        <select name="user_id" id="user_id" class="form-select border-start-0 @error('user_id') is-invalid @enderror" required onchange="updatePreviewUser(this)">
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }} data-name="{{ $user->name ?? $user->nom }}">
                                                    {{ $user->name ?? $user->nom }} ({{ $user->role->nom ?? 'Agent' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Date & Heure --}}
                            <div class="col-md-5">
                                <label for="date_ouverture" class="form-label fw-bold text-dark mb-1">
                                    Date & Heure d'Ouverture <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="calendar" class="lucide-sm text-muted"></i></span>
                                    <input type="datetime-local" name="date_ouverture" id="date_ouverture" class="form-control border-start-0 @error('date_ouverture') is-invalid @enderror" value="{{ old('date_ouverture', date('Y-m-d\TH:i')) }}" required onchange="updatePreviewDate(this.value)">
                                </div>
                                <div class="form-text small text-muted">Horodatage officiel de démarrage.</div>
                                @error('date_ouverture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 2 : Fond de Caisse Initial (Le centre névralgique) --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #e3f3ee; color: #0f6b5f;">
                                <i data-lucide="banknote" class="lucide-sm"></i>
                            </span>
                            <h6 class="card-title mb-0 fw-bold text-dark">Fond de Caisse Initial (Monnaie de Départ)</h6>
                        </div>
                        <span class="small text-muted font-mono">Espèces liquides</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="p-3 p-md-4 rounded-3 mb-3" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border: 2px dashed #cbd5e1;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                <label for="fonds_initial" class="form-label fw-bold text-dark mb-0 fs-6">
                                    Montant du fond initial dans le tiroir <span class="text-danger">*</span>
                                </label>
                                <span class="text-muted small">Ce montant sera comptabilisé au solde de départ</span>
                            </div>

                            <div class="input-group input-group-lg shadow-sm mb-3">
                                <span class="input-group-text bg-white border-end-0 text-muted px-3">
                                    <i data-lucide="wallet" class="lucide text-teal"></i>
                                </span>
                                <input type="number" step="100" min="0" name="fonds_initial" id="fonds_initial"
                                       class="form-control form-control-lg border-start-0 border-end-0 font-mono fw-bold text-dark fs-2 @error('fonds_initial') is-invalid @enderror"
                                       value="{{ old('fonds_initial', 10000) }}" placeholder="0" required
                                       oninput="updateFondsPreview(this.value)">
                                <span class="input-group-text font-mono fw-bold bg-white text-dark border-start-0 px-3 fs-5" style="color: #0f766e !important;">
                                    FCFA
                                </span>
                            </div>

                            {{-- Préréglages rapides fréquents --}}
                            <div>
                                <div class="small fw-semibold text-muted mb-2 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                                    Montants fréquents suggérés :
                                </div>
                                <div class="d-flex flex-wrap gap-2" id="presetChips">
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(0, this)">
                                        0 F <small class="opacity-75">(Sans fond)</small>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary text-white font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(10000, this)">
                                        10 000 F
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(20000, this)">
                                        20 000 F
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(25000, this)">
                                        25 000 F
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(50000, this)">
                                        50 000 F
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-mono px-3 py-1 rounded-pill preset-chip" onclick="setFonds(100000, this)">
                                        100 000 F
                                    </button>
                                </div>
                            </div>
                            @error('fonds_initial')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-light text-muted small">
                            <i data-lucide="info" class="lucide-sm text-primary flex-shrink-0"></i>
                            <span>Le fond initial correspond à la monnaie disponible pour les rendus aux patients dès les premiers tickets.</span>
                        </div>
                    </div>
                </div>

                {{-- Carte 3 : Poste de travail & Remarques --}}
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                        <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #fef3c7; color: #92400e;">
                            <i data-lucide="map-pin" class="lucide-sm"></i>
                        </span>
                        <h6 class="card-title mb-0 fw-bold text-dark">Emplacement & Remarques de Vacation</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="observation" class="form-label fw-bold text-dark mb-1">
                                Guichet / Emplacement
                            </label>
                            <input type="text" name="observation" id="observation" class="form-control @error('observation') is-invalid @enderror" value="{{ old('observation', 'Guichet 1 - Principal') }}" placeholder="Ex: Guichet 1 - Vacation Matin" oninput="updatePostePreview(this.value)">
                            @error('observation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="small text-muted align-self-center me-1">Raccourcis :</span>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 small" onclick="setPoste('Guichet 1 - Principal')">Guichet 1 - Principal</button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 small" onclick="setPoste('Guichet 2 - Accueil')">Guichet 2 - Accueil</button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 small" onclick="setPoste('Guichet Urgences')">Guichet Urgences</button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 small" onclick="setPoste('Vacation Soir / Garde')">Vacation Soir / Garde</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne Latérale (4 cols) : Récapitulatif en Direct & Validation --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 20px;">
                    {{-- Récapitulatif prévisionnel --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden" style="border-top: 4px solid var(--primary-color) !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                                <span class="fw-bold text-dark fs-6">Bilan d'Ouverture</span>
                                <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #12a594;"></span>
                                    <span>Prêt à démarrer</span>
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="small text-muted mb-1">Fond de Caisse Initial Déclaré</div>
                                <div class="font-mono fs-3 fw-bold" style="color: var(--primary-color);" id="preview_fonds">
                                    10 000 FCFA
                                </div>
                            </div>

                            <div class="list-group list-group-flush small mb-4">
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between bg-transparent">
                                    <span class="text-muted">Caissier(ère) :</span>
                                    <strong class="text-dark text-truncate ms-2" id="preview_user">
                                        {{ auth()->user()->name ?? auth()->user()->nom }}
                                    </strong>
                                </div>
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between bg-transparent">
                                    <span class="text-muted">Poste / Guichet :</span>
                                    <strong class="text-dark text-truncate ms-2" id="preview_poste">
                                        Guichet 1 - Principal
                                    </strong>
                                </div>
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between bg-transparent">
                                    <span class="text-muted">Horodatage :</span>
                                    <strong class="text-dark ms-2 font-mono" id="preview_date">
                                        {{ date('d/m/Y H:i') }}
                                    </strong>
                                </div>
                            </div>

                            {{-- Bouton de soumission principal --}}
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-3 mb-2">
                                <i data-lucide="check-circle" class="lucide"></i>
                                <span class="fs-6">Confirmer & Ouvrir la Caisse</span>
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('caisses.index') }}" class="text-muted small text-decoration-none">
                                    <i data-lucide="x" class="lucide-sm align-middle me-1"></i>
                                    Annuler et revenir
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Bonnes pratiques de sécurité --}}
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i data-lucide="shield-check" class="lucide text-success"></i>
                            <span class="fw-bold text-dark small">Bonnes Pratiques de Caisse</span>
                        </div>
                        <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                            <li class="d-flex align-items-start gap-2">
                                <i data-lucide="check" class="lucide-sm text-success flex-shrink-0 mt-1"></i>
                                <span>Comptez physiquement les coupures avant de valider le fond de caisse.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i data-lucide="check" class="lucide-sm text-success flex-shrink-0 mt-1"></i>
                                <span>Chaque encaissement sera strictement lié à votre session personnelle.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i data-lucide="check" class="lucide-sm text-success flex-shrink-0 mt-1"></i>
                                <span>En fin de journée, procédez à la clôture et imprimez votre Rapport Z.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
}

function updateFondsPreview(val) {
    var num = parseFloat(val) || 0;
    var preview = document.getElementById('preview_fonds');
    if (preview) {
        preview.textContent = formatMoney(num);
    }
}

function setFonds(amount, btn) {
    var input = document.getElementById('fonds_initial');
    if (input) {
        input.value = amount;
        updateFondsPreview(amount);
    }
    document.querySelectorAll('.preset-chip').forEach(function(el) {
        el.classList.remove('btn-primary', 'text-white');
        el.classList.add('btn-outline-secondary');
    });
    if (btn) {
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary', 'text-white');
    }
}

function setPoste(poste) {
    var input = document.getElementById('observation');
    if (input) {
        input.value = poste;
        updatePostePreview(poste);
    }
}

function updatePostePreview(val) {
    var p = document.getElementById('preview_poste');
    if (p) {
        p.textContent = (val && val.trim()) ? val.trim() : 'Non précisé';
    }
}

function updatePreviewDate(val) {
    var p = document.getElementById('preview_date');
    if (p && val) {
        var d = new Date(val);
        if (!isNaN(d.getTime())) {
            var day = String(d.getDate()).padStart(2, '0');
            var month = String(d.getMonth() + 1).padStart(2, '0');
            var year = d.getFullYear();
            var hours = String(d.getHours()).padStart(2, '0');
            var mins = String(d.getMinutes()).padStart(2, '0');
            p.textContent = day + '/' + month + '/' + year + ' ' + hours + ':' + mins;
        }
    }
}

function updatePreviewUser(select) {
    var p = document.getElementById('preview_user');
    if (p && select) {
        var opt = select.options[select.selectedIndex];
        p.textContent = opt ? opt.getAttribute('data-name') : select.value;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('fonds_initial');
    if (input) {
        updateFondsPreview(input.value);
    }
    var obs = document.getElementById('observation');
    if (obs) {
        updatePostePreview(obs.value);
    }
    var d = document.getElementById('date_ouverture');
    if (d) {
        updatePreviewDate(d.value);
    }
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endsection

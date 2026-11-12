@extends('layout')

@section('title', 'Fiche du Dr ' . $medecin->prenom . ' ' . $medecin->nom . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane --}}
    <div class="small text-muted mb-2 d-flex align-items-center gap-1">
        <span>Ressources Médicales</span>
        <span class="opacity-50">/</span>
        <a href="{{ route('medecins.index') }}" class="text-decoration-none text-muted">Praticiens</a>
        <span class="opacity-50">/</span>
        <span class="fw-semibold text-dark">Fiche Praticien</span>
    </div>

    {{-- BANNIÈRE HERO DU PRATICIEN --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
        <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                {{-- Avatar --}}
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 72px; height: 72px; font-size: 1.5rem; background: linear-gradient(135deg, #0f766e, #14b8a6); flex-shrink: 0;">
                    {{ strtoupper(substr($medecin->prenom, 0, 1) . substr($medecin->nom, 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h3 text-dark fw-bold mb-0">Dr {{ $medecin->prenom }} {{ strtoupper($medecin->nom) }}</h1>
                        @if ($medecin->statut)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold rounded-pill">🟢 En Exercice</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-bold rounded-pill">⚪ Inactif</span>
                        @endif
                        <span class="badge bg-primary-subtle text-primary border-0 px-2 py-1 fw-semibold">
                            {{ ucfirst($medecin->type_remuneration) }} 
                            @if($medecin->type_remuneration === 'pourcentage' || $medecin->type_remuneration === 'mixte')
                                ({{ (float)$medecin->pourcentage }}%)
                            @endif
                        </span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-1">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i data-lucide="stethoscope" class="lucide-xs text-primary"></i>
                            <strong class="text-dark">{{ $medecin->specialite }}</strong>
                        </span>
                        <span>•</span>
                        <span class="d-inline-flex align-items-center gap-1 font-mono">
                            <i data-lucide="phone" class="lucide-xs text-primary"></i>
                            {{ $medecin->telephone }}
                        </span>
                        <span>•</span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <i data-lucide="calendar" class="lucide-xs text-muted"></i>
                            Inscrit le {{ $medecin->created_at ? $medecin->created_at->format('d/m/Y') : '--' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('remunerations.create') }}?medecin_id={{ $medecin->id }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                    <i data-lucide="calculator" class="lucide-sm"></i>
                    <span>Rétrocession / Paie</span>
                </a>
                <a href="{{ route('tickets.create') }}?medecin_id={{ $medecin->id }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                    <i data-lucide="plus" class="lucide-sm"></i>
                    <span>Nouveau Ticket</span>
                </a>
                <a href="{{ route('medecins.edit', $medecin) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i data-lucide="edit-3" class="lucide-sm"></i>
                    <span>Modifier</span>
                </a>
                <a href="{{ route('medecins.index') }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" class="lucide-sm"></i>
                    <span>Liste</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 4 CARTES KPI D'ACTIVITÉ --}}
    <div class="row g-3 mb-4">
        {{-- Total Soins --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Prestations & Soins</span>
                        <h3 class="h3 font-mono fw-bold text-dark mb-0 mt-1">{{ number_format($totalActes, 0, ',', ' ') }}</h3>
                        <span class="extra-small text-muted">Actes enregistrés au dossier</span>
                    </div>
                    <div class="rounded-3 p-2 bg-info-subtle text-info">
                        <i data-lucide="activity" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chiffre d'Affaires Brut --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Recettes Générées</span>
                        <h3 class="h3 font-mono fw-bold text-dark mb-0 mt-1">{{ number_format($totalChiffreAffaires, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small></h3>
                        <span class="extra-small text-muted">Chiffre d'affaires brut clinique</span>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                        <i data-lucide="receipt" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Rétrocessions Dues --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Honoraires Praticien</span>
                        <h3 class="h3 font-mono fw-bold text-success mb-0 mt-1">{{ number_format($totalPartMedecin, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small></h3>
                        <span class="extra-small text-success">Part totale due au praticien</span>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success">
                        <i data-lucide="wallet" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Solde en Attente --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Solde en Attente</span>
                        <h3 class="h3 font-mono fw-bold {{ $soldeDu > 0 ? 'text-warning' : 'text-muted' }} mb-0 mt-1">
                            {{ number_format($soldeDu, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                        </h3>
                        <span class="extra-small text-muted">Déjà réglé : {{ number_format($totalPaye, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="rounded-3 p-2 {{ $soldeDu > 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-muted' }}">
                        <i data-lucide="clock" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION PRINCIPALE : DÉTAILS CONTRACTUELS & ONGLETS D'ACTIVITÉ --}}
    <div class="row g-4">
        {{-- CARTE DE GAUCHE : CONDITIONS DU CONTRAT --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="file-badge" class="lucide-xs"></i>
                    </span>
                    Contrat & Conditions Financières
                </h5>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Type de Rémunération :</span>
                        <span class="badge bg-secondary-subtle text-dark fw-bold">
                            @if($medecin->type_remuneration === 'pourcentage')
                                🩺 À l'Acte (Rétrocession)
                            @elseif($medecin->type_remuneration === 'fixe')
                                💼 Salaire Fixe Mensuel
                            @else
                                ⚡ Mixte (Fixe + Pourcentage)
                            @endif
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Taux Rétrocession :</span>
                        <strong class="font-mono text-primary fs-6">{{ (float)$medecin->pourcentage }} %</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Base Fixe Mensuelle :</span>
                        <strong class="font-mono text-dark">{{ number_format($medecin->salaire_fixe ?? 0, 0, ',', ' ') }} FCFA</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Part Clinique Moyenne :</span>
                        <strong class="font-mono text-muted">{{ 100 - (float)$medecin->pourcentage }} %</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Téléphone Praticien :</span>
                        <strong class="font-mono text-dark">{{ $medecin->telephone }}</strong>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Statut Clinique :</span>
                        @if ($medecin->statut)
                            <span class="badge bg-success fw-bold">Actif</span>
                        @else
                            <span class="badge bg-secondary fw-bold">Inactif</span>
                        @endif
                    </li>
                </ul>

                <div class="p-3 rounded-3 bg-light border mt-3 extra-small text-muted">
                    <div class="d-flex align-items-start gap-2">
                        <i data-lucide="shield-check" class="lucide-xs text-primary mt-1"></i>
                        <div>
                            <strong>Calcul Automatique :</strong>
                            Lors de chaque validation de ticket de caisse contenant un acte assigné au Dr {{ $medecin->nom }}, sa part est ventilée et enregistrée en temps réel.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION DE DROITE : JOURNAUX ET HISTORIQUES AVEC ONGLETS --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                {{-- Entête des Onglets --}}
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="medecinTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-3 px-4 fw-bold d-inline-flex align-items-center gap-2 border-0 border-bottom border-3 border-primary" id="prestations-tab" data-bs-toggle="tab" data-bs-target="#prestations-pane" type="button" role="tab">
                                <i data-lucide="stethoscope" class="lucide-xs"></i>
                                <span>Soins & Actes Dispensés ({{ $medecin->prestations->count() }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 px-4 fw-bold text-muted d-inline-flex align-items-center gap-2 border-0" id="remunerations-tab" data-bs-toggle="tab" data-bs-target="#remunerations-pane" type="button" role="tab">
                                <i data-lucide="wallet" class="lucide-xs"></i>
                                <span>Bulletins & Paiements ({{ $medecin->remunerations->count() }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 px-4 fw-bold text-muted d-inline-flex align-items-center gap-2 border-0" id="consultations-tab" data-bs-toggle="tab" data-bs-target="#consultations-pane" type="button" role="tab">
                                <i data-lucide="users" class="lucide-xs"></i>
                                <span>Consultations ({{ $medecin->consultations->count() }})</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="medecinTabsContent">
                        {{-- ONGLET 1 : PRESTATIONS ET SOINS RÉALISÉS --}}
                        <div class="tab-pane fade show active p-3" id="prestations-pane" role="tabpanel">
                            @if($medecin->prestations->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i data-lucide="activity" class="lucide-lg mb-2 opacity-50"></i>
                                    <p class="mb-0">Aucun acte ou soin médical n'a encore été enregistré pour ce praticien.</p>
                                    <a href="{{ route('tickets.create') }}?medecin_id={{ $medecin->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                        Créer le premier ticket avec ce médecin
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date & Heure</th>
                                                <th>Patient</th>
                                                <th>Acte / Prestation</th>
                                                <th class="text-end">Montant Brut</th>
                                                <th class="text-end">Part Clinique</th>
                                                <th class="text-end">Part Médecin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($medecin->prestations as $prestation)
                                                <tr>
                                                    <td class="font-mono text-muted">
                                                        {{ $prestation->date_prestation ? $prestation->date_prestation->format('d/m/Y H:i') : $prestation->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td>
                                                        @if($prestation->patient)
                                                            <a href="{{ route('patients.show', $prestation->patient) }}" class="text-decoration-none fw-bold text-dark">
                                                                {{ $prestation->patient->nom }} {{ $prestation->patient->prenom }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Patient anonyme</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">{{ $prestation->acte->nom ?? $prestation->type }}</span>
                                                        @if($prestation->service)
                                                            <span class="badge bg-light text-muted border extra-small">{{ $prestation->service->nom }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end font-mono fw-bold text-dark">
                                                        {{ number_format($prestation->montant, 0, ',', ' ') }} FCFA
                                                    </td>
                                                    <td class="text-end font-mono text-muted">
                                                        {{ number_format($prestation->part_clinique, 0, ',', ' ') }} FCFA
                                                    </td>
                                                    <td class="text-end font-mono fw-bold text-success">
                                                        {{ number_format($prestation->part_medecin, 0, ',', ' ') }} FCFA
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- ONGLET 2 : BULLETINS DE RÉTROCESSION & RÈGLEMENTS --}}
                        <div class="tab-pane fade p-3" id="remunerations-pane" role="tabpanel">
                            @if($medecin->remunerations->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i data-lucide="receipt" class="lucide-lg mb-2 opacity-50"></i>
                                    <p class="mb-0">Aucun bulletin de rémunération ou versement enregistré pour le moment.</p>
                                    <a href="{{ route('remunerations.create') }}?medecin_id={{ $medecin->id }}" class="btn btn-sm btn-primary mt-2">
                                        Établir un bulletin de paie / rétrocession
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Période Couverte</th>
                                                <th>Type</th>
                                                <th class="text-end">Base Actes</th>
                                                <th class="text-end">Part Médecin</th>
                                                <th>Date Paiement</th>
                                                <th>Statut</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($medecin->remunerations as $remun)
                                                <tr>
                                                    <td class="font-mono fw-semibold text-dark">
                                                        {{ $remun->periode_debut ? $remun->periode_debut->format('d/m/Y') : '--' }} au {{ $remun->periode_fin ? $remun->periode_fin->format('d/m/Y') : '--' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ ucfirst($remun->type_remuneration) }}</span>
                                                    </td>
                                                    <td class="text-end font-mono text-muted">
                                                        {{ number_format($remun->montant_base, 0, ',', ' ') }} FCFA
                                                    </td>
                                                    <td class="text-end font-mono fw-bold text-success">
                                                        {{ number_format($remun->montant_medecin, 0, ',', ' ') }} FCFA
                                                    </td>
                                                    <td class="font-mono text-muted">
                                                        {{ $remun->date_paiement ? $remun->date_paiement->format('d/m/Y') : '--' }}
                                                    </td>
                                                    <td>
                                                        @if($remun->statut === 'paye')
                                                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Payé</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1">En attente</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('remunerations.show', $remun) }}" class="btn btn-xs btn-outline-primary py-1 px-2">
                                                            <i data-lucide="eye" class="lucide-xs"></i> Voir Détails
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- ONGLET 3 : CONSULTATIONS MÉDICALES RÉCENTES --}}
                        <div class="tab-pane fade p-3" id="consultations-pane" role="tabpanel">
                            @if($medecin->consultations->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i data-lucide="clipboard" class="lucide-lg mb-2 opacity-50"></i>
                                    <p class="mb-0">Aucune consultation médicale enregistrée pour ce praticien.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Patient</th>
                                                <th>Diagnostic / Motif</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($medecin->consultations as $consult)
                                                <tr>
                                                    <td class="font-mono text-muted">
                                                        {{ $consult->date_consultation ? \Carbon\Carbon::parse($consult->date_consultation)->format('d/m/Y H:i') : $consult->created_at->format('d/m/Y') }}
                                                    </td>
                                                    <td>
                                                        @if($consult->patient)
                                                            <a href="{{ route('patients.show', $consult->patient) }}" class="text-decoration-none fw-bold text-dark">
                                                                {{ $consult->patient->nom }} {{ $consult->patient->prenom }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Inconnu</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-dark">
                                                        {{ Str::limit($consult->diagnostic ?? $consult->motif ?? 'Consultation médicale', 60) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('consultations.show', $consult) }}" class="btn btn-xs btn-outline-secondary py-1 px-2">
                                                            Voir Dossier
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
<style>
.extra-small { font-size: 0.78rem; }
.nav-tabs .nav-link.active {
    color: var(--primary-color) !important;
    border-bottom: 3px solid var(--primary-color) !important;
    background: transparent;
}
.nav-tabs .nav-link:hover {
    color: var(--primary-color);
}
</style>
@endpush

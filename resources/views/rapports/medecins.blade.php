@extends('layout')

@section('title', 'Rétrocession & Honoraires Médecins - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('rapports.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour au Centre de Rapports
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-teal-subtle text-teal border border-teal-subtle small font-mono" style="background-color: #ccfbf1; color: #0f766e;">Honoraires Médicaux</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="text-white p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="background-color: #0f766e; width: 40px; height: 40px;">
                    <i data-lucide="stethoscope" class="lucide"></i>
                </div>
                <span>Rétrocession & Honoraires des Médecins</span>
            </h1>
            <p class="text-muted mb-0 small">
                Suivi analytique, calcul des quotes-parts et liquidation des honoraires praticiens (Part Médecin vs Part Clinique).
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 no-print">
            <a href="{{ route('rapports.medecins.bordereau', request()->query()) }}" target="_blank" class="btn text-white fw-semibold d-inline-flex align-items-center gap-2 px-3 shadow-sm" style="background-color: #0f766e;">
                <i data-lucide="file-text" class="lucide-sm"></i>
                <span>Générer le Bordereau d'Honoraires (A4)</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="printer" class="lucide-sm"></i>
                <span>Imprimer l'Écran</span>
            </button>
            <x-export-buttons table-id="rapportMedecinsTable" title="Rapport_Honoraires_Medecins" filename="rapport_honoraires_medecins" />
        </div>
    </div>

    {{-- Filtres de sélection et recherche --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white no-print">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="filter" class="lucide-sm" style="color: #0f766e;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Critères de Sélection des Honoraires</h6>
                </div>
                {{-- Raccourcis de dates rapides --}}
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <span class="small text-muted me-1">Période rapide :</span>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::today()->toDateString() }}" data-end="{{ \Carbon\Carbon::today()->toDateString() }}">Aujourd'hui</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfWeek()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfWeek()->toDateString() }}">Cette Semaine</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfMonth()->toDateString() }}">Ce Mois</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString() }}">Mois Dernier</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfYear()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfYear()->toDateString() }}">Cette Année</button>
                </div>
            </div>

            <form method="GET" action="{{ route('rapports.medecins') }}" id="filterForm" class="row g-3 align-items-end">
                {{-- Sélection du médecin --}}
                <div class="col-lg-4 col-md-6">
                    <label for="medecin_id" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="user-check" class="lucide-sm text-muted"></i>
                        <span>Médecin Prestataire</span>
                    </label>
                    <select name="medecin_id" id="medecin_id" class="form-select fw-semibold">
                        <option value="">-- Tous les Médecins Prestataires --</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ ($medecinId == $medecin->id || $medecinId == $medecin->code) ? 'selected' : '' }}>
                                Dr. {{ $medecin->nom }} {{ $medecin->prenom }} ({{ $medecin->specialite ?? 'Généraliste' }}) — {{ $medecin->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Début --}}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label for="date_debut" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="calendar" class="lucide-sm text-muted"></i>
                        <span>Date de Début</span>
                    </label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control font-mono" value="{{ $dateDebut }}" required>
                </div>

                {{-- Date Fin --}}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label for="date_fin" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="calendar" class="lucide-sm text-muted"></i>
                        <span>Date de Fin</span>
                    </label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control font-mono" value="{{ $dateFin }}" required>
                </div>

                {{-- Boutons d'action --}}
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn text-white w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm" style="background-color: #0f766e;">
                        <i data-lucide="search" class="lucide-sm"></i>
                        <span>Filtrer</span>
                    </button>
                    <a href="{{ route('rapports.medecins') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Réinitialiser les filtres">
                        <i data-lucide="rotate-ccw" class="lucide-sm"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Synthèse Financière (Cartes KPIs Modernes) --}}
    <div class="row g-3 mb-4">
        {{-- Total Actes Réalisés --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-secondary position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Actes Dispensés</span>
                    <div class="bg-light text-secondary p-2 rounded-circle">
                        <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark font-mono">
                    {{ $totalActes }} <small class="fs-6 text-muted">acte(s)</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Consultations & interventions
                </p>
            </div>
        </div>

        {{-- Total Facturé (Valeur Brute des Actes) --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Facturé Brut</span>
                    <div class="bg-info-subtle text-info p-2 rounded-circle">
                        <i data-lucide="receipt" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark font-mono">
                    {{ number_format($totalMontantActes, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Valeur publique des actes
                </p>
            </div>
        </div>

        {{-- Part Conservée par la Clinique --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Part Clinique (Plateau)</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="building-2" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-primary font-mono">
                    {{ number_format($totalPartClinique, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Part conservée par l'établissement
                </p>
            </div>
        </div>

        {{-- Total Part Due aux Médecins --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 position-relative overflow-hidden" style="border-left-color: #0f766e !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Honoraires Dus (Médecins)</span>
                    <div class="p-2 rounded-circle text-white" style="background-color: #0f766e;">
                        <i data-lucide="coins" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 font-mono" style="color: #0f766e;">
                    {{ number_format($totalPartMedecin, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h3>
                <p class="text-muted small mb-0 mt-2">
                    Net total à verser aux praticiens
                </p>
            </div>
        </div>
    </div>

    {{-- En-tête exclusif impression --}}
    <div class="d-none d-print-block mb-4 pb-3 border-bottom">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="fw-bold text-dark mb-1">CLINIQUE GAHAMBANI — BAMAKO</h3>
                <p class="small text-muted mb-0">Plateau Médico-Chirurgical & Soins Spécialisés &bull; Kati / Bamako</p>
                <h4 class="mt-3 fw-bold" style="color: #0f766e;">RÉCAPITULATIF DES HONORAIRES ET RÉTROCESSIONS MÉDICALES</h4>
                <p class="mb-0">
                    Praticien : <strong>{{ $medecinSelected ? 'Dr. ' . $medecinSelected->nom . ' ' . $medecinSelected->prenom . ' (' . $medecinSelected->specialite . ')' : 'Tous les médecins prestataires' }}</strong> &bull;
                    Période du <strong>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</strong>
                </p>
            </div>
            <div class="text-end">
                <span class="small text-muted">Édité le : {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span><br>
                <span class="small text-muted">Par : {{ auth()->user()->prenom ?? '' }} {{ auth()->user()->nom ?? 'Administration' }}</span>
            </div>
        </div>
    </div>

    {{-- Tableau des rémunérations et rétrocessions calculées --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="file-spreadsheet" class="lucide" style="color: #0f766e;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    Registre des Actes et Rétrocessions
                    @if($medecinSelected)
                        <span class="text-muted fs-6 fw-normal">— Dr. {{ $medecinSelected->nom }} {{ $medecinSelected->prenom }}</span>
                    @endif
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <span class="badge bg-light text-dark border font-mono small">
                    {{ $remunerations->total() }} acte(s) répertorié(s)
                </span>
                <a href="{{ route('rapports.medecins.bordereau', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" style="color: #0f766e; border-color: #0f766e;">
                    <i data-lucide="file-check" style="width: 14px; height: 14px;"></i>
                    <span>Bordereau A4</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="rapportMedecinsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 130px;">Date & Heure</th>
                        <th>Médecin Prestataire</th>
                        <th>Patient Bénéficiaire</th>
                        <th>Acte Médical</th>
                        <th class="text-end" style="width: 110px;">Tarif Public</th>
                        <th class="text-center" style="width: 80px;">Taux %</th>
                        <th class="text-end text-primary" style="width: 120px;">Part Clinique</th>
                        <th class="text-end fw-bold" style="width: 130px; color: #0f766e;">Honoraires Médecin</th>
                        <th class="text-center pe-3" style="width: 110px;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remunerations as $rem)
                        <tr>
                            <td class="ps-3 font-mono text-muted small">
                                {{ $rem->date_prestation ? $rem->date_prestation->format('d/m/Y H:i') : ($rem->created_at ? $rem->created_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 32px; height: 32px; background-color: #0f766e;">
                                        {{ substr($rem->medecin->nom ?? 'D', 0, 1) }}
                                    </div>
                                    <div>
                                        <strong class="text-dark">Dr. {{ $rem->medecin->nom ?? 'N/A' }} {{ $rem->medecin->prenom ?? '' }}</strong>
                                        <div class="small text-muted font-mono">{{ $rem->medecin->code ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $rem->patient->nom ?? 'Inconnu' }} {{ $rem->patient->prenom ?? '' }}
                                </div>
                                <span class="small text-muted font-mono">{{ $rem->patient->reference ?? '' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $rem->acte->nom ?? $rem->type ?? ($rem->service->nom ?? 'Acte Médical') }}
                                </span>
                            </td>
                            <td class="text-end font-mono">
                                {{ number_format($rem->montant, 0, ',', ' ') }}
                            </td>
                            <td class="text-center font-mono fw-bold" style="color: #0f766e;">
                                {{ number_format($rem->pourcentage_medecin ?? 50, 0) }}%
                            </td>
                            <td class="text-end font-mono text-primary fw-semibold">
                                {{ number_format($rem->part_clinique ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end font-mono fw-bold fs-6" style="color: #0f766e;">
                                {{ number_format($rem->part_medecin ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-center pe-3">
                                @if($rem->statut)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                        <i data-lucide="check-circle" style="width: 12px; height: 12px;" class="me-1"></i> Comptabilisé
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 small">
                                        <i data-lucide="clock" style="width: 12px; height: 12px;" class="me-1"></i> En Attente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="p-4">
                                    <i data-lucide="stethoscope" class="lucide mb-2 text-muted" style="width: 48px; height: 48px;"></i>
                                    <p class="mb-1 fw-bold text-dark">Aucune prestation médicale trouvée</p>
                                    <p class="small text-muted mb-0">Aucun acte médical enregistré pour les critères et la période sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end ps-3 fs-6 text-dark text-uppercase">
                            TOTAUX GLOBAUX :
                        </td>
                        <td class="text-end font-mono fs-6 text-dark">
                            {{ number_format($totalMontantActes, 0, ',', ' ') }} FCFA
                        </td>
                        <td></td>
                        <td class="text-end font-mono fs-6 text-primary">
                            {{ number_format($totalPartClinique, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-end font-mono fs-6 pe-3" style="color: #0f766e;">
                            {{ number_format($totalPartMedecin, 0, ',', ' ') }} FCFA
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($remunerations->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-end no-print">
                {{ $remunerations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Raccourcis de période rapide
        document.querySelectorAll('.quick-period').forEach(function (button) {
            button.addEventListener('click', function () {
                var start = this.getAttribute('data-start');
                var end = this.getAttribute('data-end');
                document.getElementById('date_debut').value = start;
                document.getElementById('date_fin').value = end;
                document.getElementById('filterForm').submit();
            });
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection

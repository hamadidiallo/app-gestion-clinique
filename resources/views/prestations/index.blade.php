@extends('layout')

@section('title', 'Soins & Actes Dispensés aux Patients - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de la page --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2 py-1 rounded-pill small fw-semibold">
                    <i data-lucide="activity" style="width: 13px; height: 13px;" class="me-1"></i>Journal Clinique
                </span>
                <span class="text-muted small">|</span>
                <span class="text-muted small">Historique des Visites & Traitements</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="stethoscope" class="text-teal"></i>Soins & Actes Dispensés aux Patients
            </h1>
            <p class="text-muted mb-0 small">Journal clinique : suivi exhaustif des consultations, examens et actes médicaux prodigués aux malades.</p>
        </div>
        <div>
            <a href="{{ route('prestation.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Enregistrer un Soin</span>
            </a>
        </div>
    </div>

    {{-- Bandeau Didactique : Différence avec le Catalogue --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light border-start border-4 border-teal">
        <div class="card-body p-3">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-white text-teal p-2 rounded-circle shadow-sm mt-1">
                    <i data-lucide="info" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">À propos de ce registre des soins</h6>
                    <p class="text-muted small mb-0">
                        Ce registre consigne <strong>en temps réel chaque soin effectivement réalisé sur un patient</strong> (consultation, pansement, radio, biologie). 
                        Il est distinct du <a href="{{ route('actes.index') }}" class="text-teal fw-semibold text-decoration-none">Catalogue des Actes</a> qui ne définit que la grille tarifaire théorique. 
                        Tout soin enregistré ici génère automatiquement un ticket de caisse et calcule la rétrocession d'honoraires du médecin.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <div class="mb-4">
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />
    </div>

    {{-- Cartes de Synthèse KPI --}}
    <div class="row g-3 mb-4">
        {{-- Total Facturé --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3 border-start border-4 border-teal h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Volume Facturé (Période)</span>
                    <div class="bg-teal-subtle text-teal p-2 rounded-circle">
                        <i data-lucide="trending-up" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark font-mono">
                    {{ number_format($stats['total_montant'] ?? $prestations->where('statut', true)->sum('montant'), 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">Total des soins actifs facturés</div>
            </div>
        </div>

        {{-- Nombre de Soins --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3 border-start border-4 border-success h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Soins & Actes Réalisés</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-success font-mono">
                    {{ $stats['nb_soins'] ?? $prestations->count() }}
                </h4>
                <div class="text-muted small">Interventions médicales tracées</div>
            </div>
        </div>

        {{-- Honoraires Praticiens --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3 border-start border-4 border-primary h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rétrocessions Médecins</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-primary font-mono">
                    {{ number_format($stats['total_part_medecin'] ?? $prestations->where('statut', true)->sum('part_medecin'), 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">Part revenant aux praticiens</div>
            </div>
        </div>

        {{-- Patients Reçus --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3 border-start border-4 border-info h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Patients Soignés</span>
                    <div class="bg-info-subtle text-info p-2 rounded-circle">
                        <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark font-mono">
                    {{ $stats['nb_patients'] ?? $prestations->pluck('patient_id')->unique()->count() }}
                </h4>
                <div class="text-muted small">Patients uniques pris en charge</div>
            </div>
        </div>
    </div>

    {{-- Tableau des prestations --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="clipboard-list" class="text-teal"></i>
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">Journal des Soins & Interventions Médicales</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="prestationsTable" title="Journal des Soins aux Patients" filename="soins_patients" />
                <span class="badge bg-light text-muted border font-mono">{{ $prestations->count() }} soin(s)</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="prestationsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="width: 50px;">#</th>
                            <th class="py-3" style="width: 130px;">Date & Heure</th>
                            <th class="py-3">Patient</th>
                            <th class="py-3">Acte Réalisé</th>
                            <th class="py-3">Service</th>
                            <th class="py-3">Praticien Traitant</th>
                            <th class="text-end py-3">Montant</th>
                            <th class="text-center py-3">Statut</th>
                            <th class="text-center pe-4 py-3" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestations as $prestation)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-mono text-muted small">#{{ $prestation->id }}</span>
                                </td>
                                <td>
                                    <span class="font-mono text-muted small d-inline-flex align-items-center gap-1">
                                        <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                        {{ $prestation->date_prestation ? \Carbon\Carbon::parse($prestation->date_prestation)->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($prestation->patient)
                                        <a href="{{ route('patient.show', $prestation->patient) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $prestation->patient->prenom }} {{ $prestation->patient->nom }}
                                        </a>
                                        <div class="small text-muted font-mono">
                                            {{ $prestation->patient->numero_dossier ?? '' }}
                                        </div>
                                    @else
                                        <span class="text-muted small">Patient Inconnu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $prestation->acte->nom ?? $prestation->type ?? 'Soin général' }}
                                    </div>
                                    @if($prestation->acte && $prestation->acte->code)
                                        <span class="badge bg-light text-muted border font-mono small">
                                            {{ $prestation->acte->code }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($prestation->service)
                                        <span class="badge bg-light text-dark border font-mono small">
                                            {{ $prestation->service->nom }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($prestation->medecin)
                                        <span class="text-dark small fw-semibold">
                                            Dr. {{ $prestation->medecin->prenom }} {{ $prestation->medecin->nom }}
                                        </span>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            {{ $prestation->medecin->specialite ?? 'Généraliste' }}
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Non attribué</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="font-mono fw-bold text-dark">
                                        {{ number_format($prestation->montant, 0, ',', ' ') }}
                                    </span>
                                    <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-center">
                                    @if ($prestation->statut)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                            Effectué
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small">
                                            Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('prestations.show', $prestation) }}" class="btn btn-sm btn-outline-teal" title="Voir les détails">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <a href="{{ route('prestations.edit', $prestation) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                            <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePrestationModal{{ $prestation->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>

                                    {{-- Modal confirmation suppression --}}
                                    <div class="modal fade text-start" id="deletePrestationModal{{ $prestation->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    Êtes-vous sûr de vouloir supprimer la prestation <strong>#{{ $prestation->id }}</strong> 
                                                    du patient <strong>{{ $prestation->patient->nom ?? 'Inconnu' }}</strong> pour un montant de <strong>{{ number_format($prestation->montant, 0, ',', ' ') }} FCFA</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light px-4 py-3">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('prestations.destroy', $prestation) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Supprimer définitivement</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-lucide="activity" style="width: 48px; height: 48px;" class="mb-3 text-muted opacity-50"></i>
                                        <h6 class="fw-bold mb-1">Aucun soin médical enregistré sur cette période</h6>
                                        <p class="small text-muted mb-3">
                                            Dès qu'un patient consulte ou reçoit un acte soignant, la ligne s'affichera automatiquement dans ce journal.
                                        </p>
                                        <a href="{{ route('prestation.create') }}" class="btn btn-teal btn-sm fw-semibold">
                                            <i data-lucide="plus-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                                            Enregistrer un Soin
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layout')

@section('title', 'Rémunérations & Rétrocessions Médecins - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2 py-1 rounded-pill small fw-semibold">
                    <i data-lucide="coins" style="width: 13px; height: 13px;" class="me-1"></i>Honoraires & Paies
                </span>
                <span class="text-muted small">|</span>
                <span class="text-muted small">Module Financier & Ressources Humaines</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="calculator" class="text-teal"></i>Rémunérations & Rétrocessions Médecins
            </h1>
            <p class="text-muted mb-0 small">Décompte périodique des honoraires, parts praticiens et quotes-parts conservées par la clinique.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('remunerations.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Générer un Décompte</span>
            </a>
        </div>
    </div>

    {{-- Bandeau Explicatif : Actes vs Prestations vs Rétrocession --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light border-start border-4 border-teal">
        <div class="card-body p-3">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-white text-teal p-2 rounded-circle shadow-sm mt-1">
                    <i data-lucide="info" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">Comprendre le fonctionnement des Rétrocessions</h6>
                    <p class="text-muted small mb-0">
                        Chaque soin dispensé à un patient est une <strong>prestation</strong> reliée à un <strong>acte du catalogue</strong>. 
                        Le système calcule automatiquement la quote-part du médecin (pourcentage contractuel ou forfait) et la part de la clinique. 
                        Sur cette page, vous générez la <strong>fiche de paie globale</strong> du médecin pour la période de votre choix en cumulant l'ensemble de ses prestations réalisées.
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
        {{-- Total Rétrocession Payée --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rétrocessions Réglées</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-success font-mono">
                    {{ number_format($stats['total_paye'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">Paiements effectifs validés</div>
            </div>
        </div>

        {{-- Rétrocessions en Attente --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">En Attente de Règlement</span>
                    <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                        <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-warning font-mono">
                    {{ number_format($stats['total_attente'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">Fiches calculées non soldées</div>
            </div>
        </div>

        {{-- Part Conservée Clinique --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Part Clinique (Période)</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="building" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-primary font-mono">
                    {{ number_format($stats['total_clinique'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">Marge conservée établissement</div>
            </div>
        </div>

        {{-- Base Totale Facturée --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-teal h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Volume d'Actes Réalisés</span>
                    <div class="bg-teal-subtle text-teal p-2 rounded-circle">
                        <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark font-mono">
                    {{ number_format($stats['total_base'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                </h4>
                <div class="text-muted small">{{ $remunerations->count() }} décompte(s) sur la période</div>
            </div>
        </div>
    </div>

    {{-- Tableau des Rémunérations --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="file-spreadsheet" class="text-teal"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Registre des Décomptes & Fiches de Rémunération</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="remunerationsTable" title="Registre des Rémunérations Médecins" filename="remunerations_medecins" />
                <span class="badge bg-light text-dark border fs-7">{{ $remunerations->count() }} fiche(s)</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="remunerationsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Médecin Praticien</th>
                            <th>Période Couverte</th>
                            <th>Type de Contrat</th>
                            <th class="text-end">Base Facturée</th>
                            <th class="text-end">Part Médecin</th>
                            <th class="text-end">Part Clinique</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-3" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remunerations as $remun)
                            <tr>
                                <td class="ps-3 font-mono fw-semibold text-dark small">{{ $remun->reference ?? ('#'.$remun->id) }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-teal-subtle text-teal rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                                            {{ strtoupper(substr($remun->medecin->prenom ?? 'M', 0, 1) . substr($remun->medecin->nom ?? 'D', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('remunerations.show', $remun) }}" class="fw-bold text-dark text-decoration-none">
                                                Dr. {{ $remun->medecin ? $remun->medecin->prenom . ' ' . $remun->medecin->nom : 'Médecin non trouvé' }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ $remun->medecin->specialite ?? 'Généraliste' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-nowrap">
                                        <i data-lucide="calendar" style="width: 14px; height: 14px;" class="text-muted"></i>
                                        <span class="small">
                                            {{ $remun->periode_debut ? $remun->periode_debut->format('d/m/Y') : '-' }} au {{ $remun->periode_fin ? $remun->periode_fin->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($remun->type_remuneration === 'salaire_fixe')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">
                                            Salaire Fixe
                                        </span>
                                    @else
                                        <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2 py-1 rounded-pill">
                                            Pourcentage ({{ (int)$remun->pourcentage }}%)
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end font-mono fw-semibold text-dark">
                                    {{ number_format($remun->montant_base, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-end font-mono">
                                    <span class="fw-bold text-success fs-6">
                                        {{ number_format($remun->montant_medecin, 0, ',', ' ') }}
                                    </span>
                                    <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-end font-mono text-primary fw-semibold">
                                    {{ number_format($remun->montant_clinique, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-center">
                                    @if(in_array($remun->statut, ['payee', 'paye']))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="check-check" style="width: 12px; height: 12px;"></i>
                                            Payé
                                        </span>
                                    @elseif(in_array($remun->statut, ['calculee', 'en_attente']))
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                            En attente
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                            Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('remunerations.show', $remun) }}" class="btn btn-sm btn-outline-teal" title="Voir les détails et actes">
                                            <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                        </a>

                                        @if(!in_array($remun->statut, ['payee', 'paye']))
                                            <form action="{{ route('remunerations.payer', $remun) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer le paiement de {{ number_format($remun->montant_medecin, 0, ',', ' ') }} FCFA au Dr. {{ $remun->medecin->nom ?? '' }} ?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Valider le paiement">
                                                    <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('remunerations.edit', $remun) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                            <i data-lucide="edit-2" style="width: 15px; height: 15px;"></i>
                                        </a>

                                        @if($remun->statut !== 'payee')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRemunModal{{ $remun->id }}" title="Supprimer">
                                                <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Modal de confirmation suppression --}}
                                    @if($remun->statut !== 'payee')
                                        <div class="modal fade text-start" id="deleteRemunModal{{ $remun->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title fs-6">
                                                            <i data-lucide="alert-triangle" class="me-2"></i>Confirmation de suppression
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        Êtes-vous sûr de vouloir supprimer la fiche de paie #{{ $remun->id }} du <strong>Dr. {{ $remun->medecin->nom ?? '' }}</strong> pour un montant de <strong>{{ number_format($remun->montant_medecin, 0, ',', ' ') }} FCFA</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('remunerations.destroy', $remun) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">Supprimer définitivement</button>
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
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-lucide="calculator" style="width: 48px; height: 48px;" class="mb-3 text-muted opacity-50"></i>
                                        <h6 class="fw-bold mb-1">Aucune fiche de paie enregistrée</h6>
                                        <p class="small text-muted mb-3">Aucun décompte de rétrocession n'a été calculé pour cette période.</p>
                                        <a href="{{ route('remunerations.create') }}" class="btn btn-teal btn-sm fw-semibold">
                                            <i data-lucide="plus-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                                            Générer un premier décompte
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

@extends('layout')

@section('title', 'Historique des Paiements & Règlements - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-credit-card-2-front me-2"></i>Historique des Paiements & Encaissements
            </h1>
            <p class="text-muted mb-0">Registre chronologique des règlements effectués au guichet de caisse.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('paiements.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Nouveau Règlement
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-receipt"></i> Tickets à Régler
            </a>
        </div>
    </div>

    {{-- Composant de filtrage par période et statut --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" :statuses="$statuses" />

    {{-- Cartes de synthèse financière des règlements --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Encaissements Validés</span>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($paiements->where('statut', 'valide')->sum('montant_impute'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Nombre de Règlements</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ $paiements->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3">
                        <i class="bi bi-arrow-return-left fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Monnaie Rendue (Trop-Perçu)</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($paiements->sum('montant_rendu'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des règlements --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-check text-primary me-2"></i>Journal des Encaissements
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="paiementsTable" title="Journal des Encaissements" filename="paiements" />
                <span class="badge bg-light text-dark border fs-7">{{ $paiements->count() }} paiement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="paiementsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>Référence</th>
                            <th><i class="bi bi-receipt text-muted me-1"></i>N° Ticket</th>
                            <th><i class="bi bi-wallet text-muted me-1"></i>Mode Paiement</th>
                            <th><i class="bi bi-person text-muted me-1"></i>Payeur</th>
                            <th class="text-end"><i class="bi bi-arrow-down text-muted me-1"></i>Montant Reçu</th>
                            <th class="text-end"><i class="bi bi-check-lg text-muted me-1"></i>Imputé (Net)</th>
                            <th class="text-end"><i class="bi bi-arrow-return-left text-muted me-1"></i>Monnaie Rendue</th>
                            <th><i class="bi bi-clock text-muted me-1"></i>Date Règlement</th>
                            <th class="text-center"><i class="bi bi-flag text-muted me-1"></i>Statut</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-dark font-monospace">#{{ $paiement->reference }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary font-monospace">
                                        {{ $paiement->ticket ? '#' . $paiement->ticket->reference : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        {{ $paiement->modePaiement->nom ?? 'Espèces' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ ucfirst($paiement->type_payeur) }}</div>
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ number_format($paiement->montant_recu, 0, ',', ' ') }} FBU</td>
                                <td class="text-end fw-bold text-success">{{ number_format($paiement->montant_impute, 0, ',', ' ') }} FBU</td>
                                <td class="text-end text-warning fw-semibold">{{ number_format($paiement->montant_rendu, 0, ',', ' ') }} FBU</td>
                                <td>
                                    <small class="text-dark fw-semibold">
                                        {{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if (in_array($paiement->statut, ['valide', 'actif', '1']))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Validé / Actif
                                        </span>
                                    @elseif ($paiement->statut == 'en_attente')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                            En attente
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            Inactif / Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('paiements.show', $paiement) }}" class="btn btn-outline-primary" title="Reçu / Fiche">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <a href="{{ route('paiements.edit', $paiement) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePaiementModal{{ $paiement->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deletePaiementModal{{ $paiement->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir annuler/supprimer ce règlement <strong>#{{ $paiement->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('paiements.destroy', $paiement) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-credit-card fs-1 d-block mb-2 text-secondary"></i>
                                    Aucun règlement répertorié pour cette période. Cliquez sur "Nouveau Règlement" pour en enregistrer un.
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

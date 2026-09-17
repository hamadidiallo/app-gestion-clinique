@extends('layout')

@section('title', 'Historique des Paiements & Règlements - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="credit-card" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Historique des Paiements & Encaissements</span>
            </h1>
            <p class="text-muted mb-0 small">Registre chronologique des règlements effectués au guichet de caisse.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('paiements.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Nouveau Règlement</span>
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="receipt" style="width: 1rem; height: 1rem;"></i>
                <span>Tickets à Régler</span>
            </a>
        </div>
    </div>

    {{-- Composant de filtrage par période et statut --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" :statuses="$statuses" />

    {{-- Cartes de synthèse financière inspirées de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        {{-- Total Encaissements Validés --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="banknote" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Encaissé</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($paiements->where('statut', 'valide')->sum('montant_impute'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Encaissements Validés</div>
            </div>
        </div>

        {{-- Nombre de Règlements --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #eef1f4; color: #334155;">
                        <i data-lucide="receipt" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge bg-light text-muted border">Opérations</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $paiements->count() }}
                </div>
                <div class="text-muted small">Nombre de Règlements Enregistrés</div>
            </div>
        </div>

        {{-- Total Monnaie Rendue --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="coins" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-warning-pill">Trop-perçu</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($paiements->sum('montant_rendu'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Monnaie Rendue aux Patients</div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des règlements --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Journal des Encaissements</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="paiementsTable" title="Journal des Encaissements" filename="paiements" />
                <span class="badge bg-light text-muted border font-mono">{{ $paiements->count() }} paiement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="paiementsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Référence</th>
                            <th>N° Ticket</th>
                            <th>Mode Paiement</th>
                            <th>Payeur</th>
                            <th class="text-end">Montant Reçu</th>
                            <th class="text-end">Imputé (Net)</th>
                            <th class="text-end">Monnaie Rendue</th>
                            <th>Date Règlement</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('paiements.show', $paiement) }}" class="fw-bold text-teal font-mono text-decoration-none">
                                        #{{ $paiement->reference }}
                                    </a>
                                </td>
                                <td>
                                    @if($paiement->ticket)
                                        <a href="{{ route('tickets.show', $paiement->ticket) }}" class="font-mono text-dark text-decoration-none fw-semibold">
                                            #{{ $paiement->ticket->reference }}
                                        </a>
                                    @else
                                        <span class="text-muted font-mono">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono">
                                        {{ $paiement->modePaiement->nom ?? 'Espèces' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ ucfirst($paiement->type_payeur) }}</div>
                                </td>
                                <td class="text-end font-mono text-dark">{{ number_format($paiement->montant_recu, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end fw-bold text-success font-mono">{{ number_format($paiement->montant_impute, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end font-mono text-muted">{{ number_format($paiement->montant_rendu, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="small text-muted font-mono">
                                        {{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if (in_array($paiement->statut, ['valide', 'actif', '1']))
                                        <span class="badge badge-success-pill">
                                            Validé
                                        </span>
                                    @elseif ($paiement->statut == 'en_attente')
                                        <span class="badge badge-warning-pill">
                                            En attente
                                        </span>
                                    @else
                                        <span class="badge badge-danger-pill">
                                            Annulé
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('paiements.show', $paiement) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Reçu / Fiche">
                                            <i data-lucide="file-text" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('paiements.edit', $paiement) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deletePaiementModal{{ $paiement->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deletePaiementModal{{ $paiement->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation d'annulation</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start p-4">
                                                    Êtes-vous sûr de vouloir annuler/supprimer ce règlement <strong class="font-mono text-dark">#{{ $paiement->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('paiements.destroy', $paiement) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger fw-semibold">Confirmer la suppression</button>
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
                                    <i data-lucide="inbox" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-muted"></i>
                                    Aucun règlement répertorié pour cette période.<br>
                                    <a href="{{ route('paiements.create') }}" class="btn btn-sm btn-teal mt-2">
                                        + Enregistrer un premier règlement
                                    </a>
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

@extends('layout')

@section('title', 'Gestion des Dépenses - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="trending-down" style="width: 1.5rem; height: 1.5rem;" class="text-danger"></i>
                <span>Registre des Dépenses & Charges</span>
            </h1>
            <p class="text-muted mb-0 small">Suivi et contrôle des décaissements et achats de la clinique.</p>
        </div>
        <div>
            <a href="{{ route('depenses.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Enregistrer une Dépense</span>
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

    {{-- Synthèse de la Période inspirée de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fce4e4; color: #b3261e;">
                        <i data-lucide="arrow-up-right" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-danger-pill">Décaissements</span>
                </div>
                <div class="font-mono fw-bold text-danger mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($depenses->where('statut', true)->sum('montant'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Dépenses Validées (Période)</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #eef1f4; color: #334155;">
                        <i data-lucide="receipt" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge bg-light text-muted border">Opérations</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $depenses->count() }}
                </div>
                <div class="text-muted small">Nombre de Dépenses Répertoriées</div>
            </div>
        </div>
    </div>

    {{-- Tableau des dépenses --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Liste des Charges</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="depensesTable" title="Registre des Dépenses & Charges" filename="depenses" />
                <span class="badge bg-light text-muted border font-mono">{{ $depenses->count() }} enregistrement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="depensesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Référence</th>
                            <th>Catégorie</th>
                            <th>Bénéficiaire / Fournisseur</th>
                            <th class="text-end">Montant</th>
                            <th>Mode de Paiement</th>
                            <th>Date Dépense</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $depense)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-teal font-mono">#{{ $depense->reference }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono">{{ $depense->categorieDepense->nom ?? 'Général' }}</span>
                                </td>
                                <td><strong class="text-dark">{{ $depense->beneficiaire }}</strong></td>
                                <td class="text-end fw-bold text-danger font-mono">-{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge bg-light text-muted border">{{ $depense->modePaiement->nom ?? 'Espèces' }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted font-mono">{{ $depense->date_depense ? \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($depense->statut)
                                        <span class="badge badge-success-pill">Validée</span>
                                    @else
                                        <span class="badge badge-danger-pill">Annulée</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('depenses.show', $depense) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir détails">
                                            <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteDepenseModal{{ $depense->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modal de suppression --}}
                                    <div class="modal fade" id="deleteDepenseModal{{ $depense->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body text-start p-4">
                                                    Êtes-vous sûr de vouloir supprimer la dépense <strong class="font-mono text-dark">#{{ $depense->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('depenses.destroy', $depense) }}" method="post" class="d-inline">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-lucide="inbox" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-muted"></i>
                                    Aucune dépense enregistrée pour cette période.<br>
                                    <a href="{{ route('depenses.create') }}" class="btn btn-sm btn-teal mt-2">
                                        + Enregistrer une première dépense
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

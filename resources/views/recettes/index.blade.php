@extends('layout')

@section('title', 'Registre des Recettes - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="trending-up" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Registre des Recettes Financières</span>
            </h1>
            <p class="text-muted mb-0 small">Comptabilisation des encaissements et revenus de la clinique.</p>
        </div>
        <div>
            <a href="{{ route('recettes.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Comptabiliser une Recette</span>
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

    {{-- Cartes de synthèse inspirées de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="banknote" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Validées</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($recettes->where('statut', true)->sum('montant'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Recettes Validées (Période)</div>
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
                    {{ $recettes->count() }}
                </div>
                <div class="text-muted small">Nombre de Recettes Enregistrées</div>
            </div>
        </div>
    </div>

    {{-- Tableau des recettes --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Liste des Recettes</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="recettesTable" title="Registre des Recettes Financières" filename="recettes" />
                <span class="badge bg-light text-muted border font-mono">{{ $recettes->count() }} enregistrement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="recettesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Référence</th>
                            <th>Ticket Associé</th>
                            <th>Paiement Associé</th>
                            <th class="text-end">Montant Recette</th>
                            <th>Date Recette</th>
                            <th>Agent Comptable</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recettes as $recette)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-teal font-mono">#{{ $recette->reference }}</span>
                                </td>
                                <td>
                                    @if($recette->ticket)
                                        <a href="{{ route('tickets.show', $recette->ticket) }}" class="font-mono text-dark text-decoration-none fw-semibold">
                                            #{{ $recette->ticket->reference }}
                                        </a>
                                    @else
                                        <span class="text-muted small font-mono">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($recette->paiement)
                                        <a href="{{ route('paiements.show', $recette->paiement) }}" class="font-mono text-teal text-decoration-none">
                                            #{{ $recette->paiement->reference }}
                                        </a>
                                    @else
                                        <span class="text-muted small font-mono">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success font-mono">+{{ number_format($recette->montant, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="small text-muted font-mono">{{ $recette->date_recette ? \Carbon\Carbon::parse($recette->date_recette)->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td>{{ $recette->user->name ?? $recette->user->nom ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if ($recette->statut)
                                        <span class="badge badge-success-pill">Validée</span>
                                    @else
                                        <span class="badge badge-danger-pill">Annulée</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('recettes.show', $recette) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir détails">
                                            <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteRecetteModal{{ $recette->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modal de suppression --}}
                                    <div class="modal fade" id="deleteRecetteModal{{ $recette->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation d'annulation</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body text-start p-4">
                                                    Êtes-vous sûr de vouloir annuler la recette <strong class="font-mono text-dark">#{{ $recette->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('recettes.destroy', $recette) }}" method="post" class="d-inline">
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
                                    Aucune recette répertoriée pour cette période.<br>
                                    <a href="{{ route('recettes.create') }}" class="btn btn-sm btn-teal mt-2">
                                        + Enregistrer une première recette
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

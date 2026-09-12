@extends('layout')

@section('title', 'Registre des Recettes - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i class="bi bi-graph-up-arrow me-2"></i>Registre des Recettes Financières
                </h1>
                <p class="text-muted mb-0">Comptabilisation des encaissements et revenus de la clinique.</p>
            </div>
            <div>
                <a href="{{ route('recettes.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Comptabiliser une Recette
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Cartes de synthèse --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                            <i class="bi bi-plus-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Recettes Validées (Période)</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($recettes->where('statut', true)->sum('montant'), 0, ',', ' ') }} FBU</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Nombre de Recettes Enregistrées</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ $recettes->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des recettes --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-check text-primary me-2"></i>Liste des Recettes
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="recettesTable" title="Registre des Recettes Financières" filename="recettes" />
                    <span class="badge bg-light text-dark border fs-7">{{ $recettes->count() }} enregistrement(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="recettesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Référence</th>
                                <th>Ticket Associé</th>
                                <th>Paiement Associé</th>
                                <th class="text-end">Montant Recette</th>
                                <th>Date Recette</th>
                                <th>Agent Comptable</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recettes as $recette)
                                <tr>
                                    <th scope="row" class="ps-3"><code>{{ $recette->reference }}</code></th>
                                    <td>
                                        @if($recette->ticket)
                                            <span class="badge bg-secondary-subtle text-dark border">#{{ $recette->ticket->reference }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($recette->paiement)
                                            <span class="badge bg-info-subtle text-info border">#{{ $recette->paiement->reference }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end"><strong class="text-success">+{{ number_format($recette->montant, 0, ',', ' ') }} FBU</strong></td>
                                    <td>{{ $recette->date_recette ? \Carbon\Carbon::parse($recette->date_recette)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $recette->user->name ?? $recette->user->nom ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if ($recette->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Validée</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Annulée</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('recettes.show', $recette) }}" class="btn btn-sm btn-outline-info" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRecetteModal{{ $recette->id }}" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteRecetteModal{{ $recette->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir annuler la recette <strong>{{ $recette->reference }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('recettes.destroy', $recette) }}" method="post" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune recette répertoriée pour cette période.
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

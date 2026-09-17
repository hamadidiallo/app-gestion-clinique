@extends('layout')

@section('title', 'Modes de Paiement & Règlement - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module avec bouton d'ajout --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="wallet" class="me-2"></i>Modes de Règlement de la Clinique
                </h1>
                <p class="text-muted mb-0">Gestion et statistiques d'utilisation des moyens de paiement (Espèces, Carte, Virement, Mobile Money...).</p>
            </div>
            <div>
                <a href="{{ route('modepaiements.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Ajouter un Mode de Paiement
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau des modes de paiement avec volumes collectés --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="list-stars" class="text-primary me-2"></i>Liste des Modes de Paiement Configurés
                </h5>
                <span class="badge bg-light text-dark border fs-7">{{ $modePaiements->count() }} mode(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Code</th>
                                <th>Nom du Mode</th>
                                <th>Description</th>
                                <th class="text-end">Règlements Période</th>
                                <th class="text-end">Montant Total Imputé</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modePaiements as $mode)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $mode->id }}</th>
                                    <td><code>{{ $mode->code }}</code></td>
                                    <td><strong>{{ $mode->nom }}</strong></td>
                                    <td>{{ $mode->description ?? '-' }}</td>
                                    <td class="text-end fw-semibold text-primary">
                                        {{ number_format($mode->paiements_count ?? 0, 0, ',', ' ') }} transaction(s)
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($mode->paiements_sum_montant_impute ?? 0, 0, ',', ' ') }} FBU
                                    </td>
                                    <td class="text-center">
                                        @if ($mode->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Actif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('modepaiements.show', $mode) }}" class="btn btn-sm btn-outline-info" title="Voir détails">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('modepaiements.edit', $mode) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModeModal{{ $mode->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteModeModal{{ $mode->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i data-lucide="alert-triangle" class="me-2"></i>Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer le mode de paiement <strong>{{ $mode->nom }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('modepaiements.destroy', $mode) }}" method="post" class="d-inline">
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
                                        <i data-lucide="wallet" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucun mode de paiement configuré. Cliquez sur "Ajouter un Mode de Paiement" pour commencer.
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

@extends('layout')

@section('title', 'Modes de Paiement & Règlement - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module avec bouton d'ajout --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="wallet" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                    <span>Modes de Règlement de la Clinique</span>
                </h1>
                <p class="text-muted mb-0 small">Gestion et statistiques d'utilisation des moyens de paiement (Espèces, Carte, Virement, Mobile Money...).</p>
            </div>
            <div>
                <a href="{{ route('modepaiements.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                    <span>Ajouter un Mode de Paiement</span>
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau des modes de paiement avec volumes collectés --}}
        <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                    <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                    <span>Liste des Modes de Paiement Configurés</span>
                </h5>
                <span class="badge bg-light text-muted border font-mono">{{ $modePaiements->count() }} mode(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #eef1f4;">
                            <tr style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7a85;">
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Nom du Mode</th>
                                <th class="py-3">Description</th>
                                <th class="text-end py-3">Règlements Période</th>
                                <th class="text-end py-3">Montant Total Imputé</th>
                                <th class="text-center py-3">Statut</th>
                                <th class="text-center pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modePaiements as $mode)
                                <tr style="border-bottom: 1px solid #eef1f4;">
                                    <td class="ps-4">
                                        <span class="font-mono text-muted">#{{ $mode->id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-mono px-2 py-1">{{ $mode->code }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 28px; height: 28px; background: #e2f1ef; color: #0f766e;">
                                                <i data-lucide="credit-card" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </div>
                                            <span>{{ $mode->nom }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $mode->description ?? '-' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="font-mono fw-semibold text-teal">
                                            {{ number_format($mode->paiements_count ?? 0, 0, ',', ' ') }} transaction(s)
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="font-mono fw-bold text-dark">
                                            {{ number_format($mode->paiements_sum_montant_impute ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($mode->statut)
                                            <span class="badge badge-success-pill">Actif</span>
                                        @else
                                            <span class="badge badge-danger-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('modepaiements.show', $mode) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir détails">
                                                <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                            <a href="{{ route('modepaiements.edit', $mode) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                                <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteModeModal{{ $mode->id }}" title="Supprimer">
                                                <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteModeModal{{ $mode->id }}" tabindex="-1" aria-hidden="true">
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
                                                        Êtes-vous sûr de vouloir supprimer le mode de paiement <strong class="text-dark">{{ $mode->nom }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light p-3">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('modepaiements.destroy', $mode) }}" method="post" class="d-inline">
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
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background: #e2f1ef; color: #0f766e;">
                                            <i data-lucide="wallet" style="width: 1.75rem; height: 1.75rem;"></i>
                                        </div>
                                        <div class="fw-semibold text-dark mb-1">Aucun mode de paiement configuré</div>
                                        <p class="text-muted small mb-3">Enregistrez les moyens de règlement acceptés par la clinique.</p>
                                        <a href="{{ route('modepaiements.create') }}" class="btn btn-teal btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                            <i data-lucide="plus-circle" style="width: 0.95rem; height: 0.95rem;"></i>
                                            <span>Ajouter un Mode de Paiement</span>
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

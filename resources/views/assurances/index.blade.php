@extends('layout')

@section('title', 'Organismes d\'Assurance - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête de la page --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                    <span>Compagnies d'Assurance & Tiers Payant</span>
                </h1>
                <p class="text-muted mb-0 small">Gestion des organismes partenaires et suivi des prises en charge.</p>
            </div>
            <div>
                <a href="{{ route('assurance.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                    <span>Créer une Assurance</span>
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau des compagnies d'assurance --}}
        <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                    <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                    <span>Organismes Partenaires</span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="assurancesTable" title="Organismes Partenaires d'Assurance" filename="assurances" />
                    <span class="badge bg-light text-muted border font-mono">{{ $assurances->count() }} assurance(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="assurancesTable" class="table table-hover align-middle mb-0">
                        <thead style="background: #eef1f4;">
                            <tr style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7a85;">
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Nom de l'Organisme</th>
                                <th class="py-3">Code</th>
                                <th class="text-center py-3">Taux de Réf.</th>
                                <th class="py-3">Téléphone</th>
                                <th class="text-end py-3">Tickets Pris en Charge</th>
                                <th class="text-end py-3">Part Assurance (Période)</th>
                                <th class="text-center py-3">Statut</th>
                                <th class="text-center pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assurances as $assurance)
                                <tr style="border-bottom: 1px solid #eef1f4;">
                                    <td class="ps-4">
                                        <span class="font-mono text-muted">#{{ $assurance->id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 28px; height: 28px; background: #e3f3ee; color: #0f6b5f;">
                                                <i data-lucide="shield-check" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </div>
                                            <span>{{ $assurance->nom }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-mono px-2 py-1">{{ $assurance->code ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success-pill font-mono">
                                            {{ number_format($assurance->taux_par_defaut ?? 80, 0) }} %
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-mono text-muted small">{{ $assurance->telephone ?? '-' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="font-mono fw-semibold text-teal">
                                            {{ number_format($assurance->tickets_count ?? 0, 0, ',', ' ') }} ticket(s)
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="font-mono fw-bold text-dark">
                                            {{ number_format($assurance->tickets_sum_montant_assurance ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($assurance->statut)
                                            <span class="badge badge-success-pill">Actif</span>
                                        @else
                                            <span class="badge badge-danger-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('assurances.show', $assurance) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir">
                                                <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                            <a href="{{ route('assurances.edit', $assurance) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                                <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteAssuranceModal{{ $assurance->id }}" title="Supprimer">
                                                <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </button>
                                        </div>

                                        {{-- Modal confirmation de suppression --}}
                                        <div class="modal fade" id="deleteAssuranceModal{{ $assurance->id }}" tabindex="-1" aria-hidden="true">
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
                                                        Êtes-vous sûr de vouloir supprimer l'assurance <strong class="text-dark">{{ $assurance->nom }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light p-3">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('assurances.destroy', $assurance) }}" method="post" class="d-inline">
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
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background: #e2f1ef; color: #0f766e;">
                                            <i data-lucide="shield-check" style="width: 1.75rem; height: 1.75rem;"></i>
                                        </div>
                                        <div class="fw-semibold text-dark mb-1">Aucune compagnie d'assurance trouvée</div>
                                        <p class="text-muted small mb-3">Enregistrez un organisme tiers payant ou assureur partenaire.</p>
                                        <a href="{{ route('assurance.create') }}" class="btn btn-teal btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                            <i data-lucide="plus-circle" style="width: 0.95rem; height: 0.95rem;"></i>
                                            <span>Créer une Assurance</span>
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

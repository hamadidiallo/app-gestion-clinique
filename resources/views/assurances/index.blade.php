@extends('layout')

@section('title', 'Liste des Assurances - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête avec titre et bouton de création d'assurance --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="shield-check" class="me-2"></i>Compagnies d'Assurance & Tiers Payant
                </h1>
                <p class="text-muted mb-0">Gestion des organismes partenaires et suivi des prises en charge.</p>
            </div>
            <div>
                <a href="{{ route('assurance.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Créer une Assurance
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau des compagnies d'assurance --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="list-check" class="text-primary me-2"></i>Organismes Partenaires
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="assurancesTable" title="Organismes Partenaires d'Assurance" filename="assurances" />
                    <span class="badge bg-light text-dark border fs-7">{{ $assurances->count() }} assurance(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="assurancesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Nom de l'Organisme</th>
                                <th>Code</th>
                                <th class="text-center">Taux de Réf.</th>
                                <th>Téléphone</th>
                                <th class="text-end">Tickets Pris en Charge</th>
                                <th class="text-end">Part Assurance (Période)</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assurances as $assurance)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $assurance->id }}</th>
                                    <td><strong>{{ $assurance->nom }}</strong></td>
                                    <td><code>{{ $assurance->code ?? '-' }}</code></td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 fs-7">
                                            {{ number_format($assurance->taux_par_defaut ?? 80, 0) }} %
                                        </span>
                                    </td>
                                    <td>{{ $assurance->telephone ?? '-' }}</td>
                                    <td class="text-end fw-semibold text-primary">
                                        {{ number_format($assurance->tickets_count ?? 0, 0, ',', ' ') }} ticket(s)
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($assurance->tickets_sum_montant_assurance ?? 0, 0, ',', ' ') }} FBU
                                    </td>
                                    <td class="text-center">
                                        @if ($assurance->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Actif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('assurances.show', $assurance) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('assurances.edit', $assurance) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAssuranceModal{{ $assurance->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modal Bootstrap de confirmation de suppression --}}
                                        <div class="modal fade" id="deleteAssuranceModal{{ $assurance->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer l'assurance <strong>{{ $assurance->nom }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('assurances.destroy', $assurance) }}" method="post" class="d-inline">
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
                                        <i data-lucide="shield-slash" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune compagnie d'assurance trouvée.
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

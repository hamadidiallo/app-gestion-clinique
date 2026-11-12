@extends('layout')

@section('title', 'Liste des Cartes d\'Assurance - ' . config('app.name'))

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête avec titre et bouton de création d'une carte d'assurance --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="card-heading" class="me-2"></i>Cartes d'Assurance Patients
                </h1>
                <p class="text-muted mb-0">Registre des numéros d'affiliation et taux de prise en charge tiers payant.</p>
            </div>
            <div>
                <a href="{{ route('carteassurance.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Créer une Carte d'Assurance
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau des cartes d'assurance --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="credit-card-2-back" class="text-primary me-2"></i>Liste des Cartes Délivrées
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="cartesAssurancesTable" title="Cartes d'Assurance Patients" filename="cartes_assurance" />
                    <span class="badge bg-light text-dark border fs-7">{{ $cartesAssurance->count() }} carte(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="cartesAssurancesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Référence</th>
                                <th>Patient Titulaire</th>
                                <th>Assurance</th>
                                <th>Taux Couverture</th>
                                <th>Début Validité</th>
                                <th>Fin Validité</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cartesAssurance as $carte)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $carte->id }}</th>
                                    <td><code>{{ $carte->reference }}</code></td>
                                    <td><strong>{{ $carte->patient ? $carte->patient->prenom . ' ' . $carte->patient->nom : '-' }}</strong></td>
                                    <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $carte->assurance ? $carte->assurance->nom : '-' }}</span></td>
                                    <td class="fw-bold text-success">{{ (float)$carte->taux_couverture }} %</td>
                                    <td>{{ $carte->date_debut ? \Carbon\Carbon::parse($carte->date_debut)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $carte->date_fin ? \Carbon\Carbon::parse($carte->date_fin)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        @if ($carte->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Actif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('cartesassurances.show', $carte) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('cartesassurances.edit', $carte) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCarteModal{{ $carte->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modal Bootstrap de confirmation de suppression --}}
                                        <div class="modal fade" id="deleteCarteModal{{ $carte->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer la carte d'assurance <strong>{{ $carte->reference }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('cartesassurances.destroy', $carte) }}" method="post" class="d-inline">
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
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i data-lucide="card-heading" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune carte d'assurance trouvée pour cette période.
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

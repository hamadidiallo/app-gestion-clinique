@extends('layout')

@section('title', 'Gestion des Dépenses - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i class="bi bi-graph-down-arrow me-2"></i>Registre des Dépenses & Charges
                </h1>
                <p class="text-muted mb-0">Suivi et contrôle des décaissements et achats de la clinique.</p>
            </div>
            <div>
                <a href="{{ route('depenses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Enregistrer une Dépense
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Synthèse de la Période --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-danger">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                            <i class="bi bi-dash-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Dépenses Validées (Période)</span>
                            <h4 class="fw-bold mb-0 text-danger">{{ number_format($depenses->where('statut', true)->sum('montant'), 0, ',', ' ') }} FBU</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="bi bi-list-check fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Nombre de Dépenses Répertoriées</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ $depenses->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des dépenses --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>Liste des Charges
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="depensesTable" title="Registre des Dépenses & Charges" filename="depenses" />
                    <span class="badge bg-light text-dark border fs-7">{{ $depenses->count() }} enregistrement(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="depensesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Référence</th>
                                <th>Catégorie</th>
                                <th>Bénéficiaire / Fournisseur</th>
                                <th class="text-end">Montant</th>
                                <th>Mode de Paiement</th>
                                <th>Date Dépense</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($depenses as $depense)
                                <tr>
                                    <th scope="row" class="ps-3"><code>{{ $depense->reference }}</code></th>
                                    <td><span class="badge bg-secondary-subtle text-dark border">{{ $depense->categorieDepense->nom ?? 'N/A' }}</span></td>
                                    <td><strong>{{ $depense->beneficiaire }}</strong></td>
                                    <td class="text-end"><strong class="text-danger">-{{ number_format($depense->montant, 0, ',', ' ') }} FBU</strong></td>
                                    <td>{{ $depense->modePaiement->nom ?? 'N/A' }}</td>
                                    <td>{{ $depense->date_depense ? \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        @if ($depense->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Validée</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Annulée</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('depenses.show', $depense) }}" class="btn btn-sm btn-outline-info" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDepenseModal{{ $depense->id }}" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteDepenseModal{{ $depense->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer la dépense <strong>{{ $depense->reference }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('depenses.destroy', $depense) }}" method="post" class="d-inline">
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
                                        Aucune dépense enregistrée pour cette période.
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

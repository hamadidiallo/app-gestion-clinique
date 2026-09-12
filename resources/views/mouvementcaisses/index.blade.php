@extends('layout')

@section('title', 'Mouvements de Caisse - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i class="bi bi-arrow-left-right me-2"></i>Mouvements d'Espèces en Caisse
                </h1>
                <p class="text-muted mb-0">Journal détaillé des entrées et sorties de fonds au guichet.</p>
            </div>
            <div>
                <a href="{{ route('mouvementcaisses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Nouveau Mouvement
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Cartes de synthèse des flux d'espèces --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                            <i class="bi bi-arrow-down-left-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Entrées d'Espèces</span>
                            <h4 class="fw-bold mb-0 text-success">
                                +{{ number_format($mouvementCaisses->where('type', 'entree')->where('statut', true)->sum('montant'), 0, ',', ' ') }} FBU
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-danger">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                            <i class="bi bi-arrow-up-right-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Sorties d'Espèces</span>
                            <h4 class="fw-bold mb-0 text-danger">
                                -{{ number_format($mouvementCaisses->where('type', 'sortie')->where('statut', true)->sum('montant'), 0, ',', ' ') }} FBU
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="bi bi-calculator fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Flux Net de Caisse</span>
                            @php
                                $entrees = $mouvementCaisses->where('type', 'entree')->where('statut', true)->sum('montant');
                                $sorties = $mouvementCaisses->where('type', 'sortie')->where('statut', true)->sum('montant');
                                $fluxNet = $entrees - $sorties;
                            @endphp
                            <h4 class="fw-bold mb-0 {{ $fluxNet >= 0 ? 'text-primary' : 'text-danger' }}">
                                {{ number_format($fluxNet, 0, ',', ' ') }} FBU
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des mouvements --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-list-stars text-primary me-2"></i>Registre des Flux Financiers
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="mvtCaissesTable" title="Registre des Flux Financiers" filename="mouvements_caisse" />
                    <span class="badge bg-light text-dark border fs-7">{{ $mouvementCaisses->count() }} mouvement(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="mvtCaissesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Référence</th>
                                <th>Caisse / Session</th>
                                <th>Agent / Caissier</th>
                                <th>Type</th>
                                <th>Origine / Motif</th>
                                <th class="text-end">Montant</th>
                                <th>Date Mouvement</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mouvementCaisses as $mvt)
                                <tr>
                                    <th scope="row" class="ps-3"><code>{{ $mvt->reference }}</code></th>
                                    <td>Session #{{ $mvt->caisse_id }}</td>
                                    <td>{{ $mvt->user ? ($mvt->user->name ?? $mvt->user->nom) : 'N/A' }}</td>
                                    <td>
                                        @if ($mvt->type == 'entree')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Entrée</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Sortie</span>
                                        @endif
                                    </td>
                                    <td>{{ $mvt->origine ?? '-' }}</td>
                                    <td class="text-end">
                                        <strong class="{{ $mvt->type == 'entree' ? 'text-success' : 'text-danger' }}">
                                            {{ $mvt->type == 'entree' ? '+' : '-' }}{{ number_format($mvt->montant, 0, ',', ' ') }} FBU
                                        </strong>
                                    </td>
                                    <td>{{ $mvt->date_mouvement ? \Carbon\Carbon::parse($mvt->date_mouvement)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        @if ($mvt->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Validé</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Annulé</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('mouvementcaisses.show', $mvt) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('mouvementcaisses.edit', $mvt) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMvtModal{{ $mvt->id }}" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteMvtModal{{ $mvt->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer le mouvement de caisse <strong>{{ $mvt->reference }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('mouvementcaisses.destroy', $mvt) }}" method="post" class="d-inline">
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
                                        <i class="bi bi-arrow-left-right fs-1 d-block mb-2 text-secondary"></i>
                                        Aucun mouvement de caisse enregistré pour cette période.
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

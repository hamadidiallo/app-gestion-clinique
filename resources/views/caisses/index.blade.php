@extends('layout')

@section('title', 'Gestion des Caisses & Guichets - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec titre, description et boutons d'actions rapides --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-cash-stack me-2"></i>Sessions de Caisses & Guichets
            </h1>
            <p class="text-muted mb-0">Contrôle des ouvertures, fermetures, encaissements et écarts de caisse.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('caisses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-door-open-fill"></i> Ouvrir une Caisse
            </a>
            <a href="{{ route('mouvementcaisses.create') }}" class="btn btn-outline-info fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-right"></i> Mouvement Espèces
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

    {{-- Synthèse sous forme de cartes d'indicateurs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-lock-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Caisses Actuellement Ouvertes</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $caisses->where('statut', 'ouverte')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Solde Encaissé Global</span>
                        <h4 class="fw-bold mb-0 text-primary">{{ number_format($caisses->sum('solde_theorique'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3">
                        <i class="bi bi-arrow-down-left-circle fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Entrées d'Espèces</span>
                        <h4 class="fw-bold mb-0 text-info">{{ number_format($caisses->sum('total_entrees'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-danger">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                        <i class="bi bi-exclamation-diamond fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Cumul des Écarts</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ number_format($caisses->sum('ecart'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif des sessions de caisses --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-journal-text text-primary me-2"></i>Historique des Sessions de Caisse
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="caissesTable" title="Historique des Sessions de Caisse" filename="sessions_caisse" />
                <span class="badge bg-light text-dark border fs-7">{{ $caisses->count() }} session(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="caissesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>Session #</th>
                            <th><i class="bi bi-person-badge text-muted me-1"></i>Caissier Responsable</th>
                            <th><i class="bi bi-calendar-check text-muted me-1"></i>Ouverture</th>
                            <th class="text-end"><i class="bi bi-cash text-muted me-1"></i>Fond Initial</th>
                            <th class="text-end"><i class="bi bi-arrow-down-right text-muted me-1"></i>Entrées</th>
                            <th class="text-end"><i class="bi bi-arrow-up-right text-muted me-1"></i>Sorties</th>
                            <th class="text-end"><i class="bi bi-calculator text-muted me-1"></i>Solde Théorique</th>
                            <th class="text-center"><i class="bi bi-flag text-muted me-1"></i>Écart</th>
                            <th class="text-center"><i class="bi bi-activity text-muted me-1"></i>Statut</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caisses as $caisse)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-dark font-monospace">#{{ $caisse->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $caisse->user->name ?? $caisse->user->nom ?? 'Non attribué' }}</div>
                                </td>
                                <td>
                                    <small class="text-dark fw-semibold">
                                        {{ $caisse->date_ouverture ? \Carbon\Carbon::parse($caisse->date_ouverture)->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td class="text-end fw-semibold text-muted">{{ number_format($caisse->fonds_initial, 0, ',', ' ') }} FBU</td>
                                <td class="text-end text-success fw-bold">+{{ number_format($caisse->total_entrees, 0, ',', ' ') }} FBU</td>
                                <td class="text-end text-danger fw-bold">-{{ number_format($caisse->total_sorties, 0, ',', ' ') }} FBU</td>
                                <td class="text-end fw-bold text-primary fs-6">{{ number_format($caisse->solde_theorique, 0, ',', ' ') }} FBU</td>
                                <td class="text-center">
                                    @if ($caisse->ecart < 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            Manquant: {{ number_format($caisse->ecart, 0, ',', ' ') }} FBU
                                        </span>
                                    @elseif ($caisse->ecart > 0)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            Excédent: +{{ number_format($caisse->ecart, 0, ',', ' ') }} FBU
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1">0 FBU</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($caisse->statut == 'ouverte')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-circle-fill me-1 small"></i> Ouverte
                                        </span>
                                    @elseif ($caisse->statut == 'fermee')
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                            Fermée
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                                            Vérifiée
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('caisses.show', $caisse) }}" class="btn btn-outline-primary" title="Voir Fiche">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('caisses.edit', $caisse) }}" class="btn btn-outline-warning" title="Clôturer/Modifier">
                                            <i class="bi bi-lock-fill"></i> Clôturer
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCaisseModal{{ $caisse->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteCaisseModal{{ $caisse->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir supprimer définitivement cette session de caisse ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('caisses.destroy', $caisse) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-cash-stack fs-1 d-block mb-2 text-secondary"></i>
                                    Aucune session de caisse pour cette période. Cliquez sur "Ouvrir une Caisse" pour commencer.
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

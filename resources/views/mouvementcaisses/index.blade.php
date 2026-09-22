@extends('layout')

@section('title', 'Mouvements de Caisse - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="arrow-up-down" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Mouvements d'Espèces en Caisse</span>
            </h1>
            <p class="text-muted mb-0 small">Journal détaillé des entrées et sorties de fonds au guichet.</p>
        </div>
        <div>
            <a href="{{ route('mouvementcaisses.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Nouveau Mouvement</span>
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

    {{-- Cartes de synthèse des flux d'espèces inspirées de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        {{-- Total Entrées --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="arrow-down-left" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Entrées</span>
                </div>
                <div class="font-mono fw-bold text-success mb-1" style="font-size: 24px; line-height: 1.2;">
                    +{{ number_format($mouvementCaisses->where('type', 'entree')->where('statut', true)->sum('montant'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Entrées d'Espèces</div>
            </div>
        </div>

        {{-- Total Sorties --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fce4e4; color: #b3261e;">
                        <i data-lucide="arrow-up-right" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-danger-pill">Sorties</span>
                </div>
                <div class="font-mono fw-bold text-danger mb-1" style="font-size: 24px; line-height: 1.2;">
                    -{{ number_format($mouvementCaisses->where('type', 'sortie')->where('statut', true)->sum('montant'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Sorties d'Espèces</div>
            </div>
        </div>

        {{-- Flux Net --}}
        <div class="col-md-4">
            @php
                $entrees = $mouvementCaisses->where('type', 'entree')->where('statut', true)->sum('montant');
                $sorties = $mouvementCaisses->where('type', 'sortie')->where('statut', true)->sum('montant');
                $fluxNet = $entrees - $sorties;
            @endphp
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="scale" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge {{ $fluxNet >= 0 ? 'badge-success-pill' : 'badge-danger-pill' }}">Solde Net</span>
                </div>
                <div class="font-mono fw-bold mb-1 {{ $fluxNet >= 0 ? 'text-teal' : 'text-danger' }}" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($fluxNet, 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Flux Net de Caisse (Période)</div>
            </div>
        </div>
    </div>

    {{-- Tableau des mouvements --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Registre des Flux Financiers</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="mvtCaissesTable" title="Registre des Flux Financiers" filename="mouvements_caisse" />
                <span class="badge bg-light text-muted border font-mono">{{ $mouvementCaisses->count() }} mouvement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="mvtCaissesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Référence</th>
                            <th>Session Caisse</th>
                            <th>Agent / Caissier</th>
                            <th>Type de Flux</th>
                            <th>Origine / Motif</th>
                            <th class="text-end">Montant</th>
                            <th>Date Mouvement</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvementCaisses as $mvt)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-teal font-mono">#{{ $mvt->reference }}</span>
                                </td>
                                <td>
                                    <span class="font-mono text-dark">Session #{{ $mvt->caisse_id }}</span>
                                </td>
                                <td>{{ $mvt->user ? ($mvt->user->name ?? $mvt->user->nom) : 'N/A' }}</td>
                                <td>
                                    @if ($mvt->type == 'entree')
                                        <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="arrow-down-left" style="width: 0.8rem; height: 0.8rem;"></i>
                                            <span>Entrée</span>
                                        </span>
                                    @else
                                        <span class="badge badge-danger-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="arrow-up-right" style="width: 0.8rem; height: 0.8rem;"></i>
                                            <span>Sortie</span>
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $mvt->origine ?? '-' }}</td>
                                <td class="text-end font-mono fw-bold {{ $mvt->type == 'entree' ? 'text-success' : 'text-danger' }}">
                                    {{ $mvt->type == 'entree' ? '+' : '-' }}{{ number_format($mvt->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td>
                                    <span class="small text-muted font-mono">{{ $mvt->date_mouvement ? \Carbon\Carbon::parse($mvt->date_mouvement)->format('d/m/Y H:i') : '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($mvt->statut)
                                        <span class="badge badge-success-pill">Validé</span>
                                    @else
                                        <span class="badge badge-danger-pill">Annulé</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('mouvementcaisses.show', $mvt) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir">
                                            <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('mouvementcaisses.edit', $mvt) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteMvtModal{{ $mvt->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modal de suppression --}}
                                    <div class="modal fade" id="deleteMvtModal{{ $mvt->id }}" tabindex="-1" aria-hidden="true">
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
                                                    Êtes-vous sûr de vouloir supprimer le mouvement de caisse <strong class="font-mono text-dark">#{{ $mvt->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('mouvementcaisses.destroy', $mvt) }}" method="post" class="d-inline">
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
                                    <i data-lucide="inbox" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-muted"></i>
                                    Aucun mouvement de caisse enregistré pour cette période.<br>
                                    <a href="{{ route('mouvementcaisses.create') }}" class="btn btn-sm btn-teal mt-2">
                                        + Enregistrer un mouvement
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

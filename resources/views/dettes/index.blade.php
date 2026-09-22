@extends('layout')

@section('title', 'Suivi des Dettes & Créances - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="alert-octagon" style="width: 1.5rem; height: 1.5rem;" class="text-danger"></i>
                <span>Suivi des Dettes & Créances Patients</span>
            </h1>
            <p class="text-muted mb-0 small">Contrôle des restes à recouvrer et échéances de paiement.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dettes.create') }}" class="btn btn-outline-danger fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Déclarer une Dette</span>
            </a>
            <a href="{{ route('paiements.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="banknote" style="width: 1rem; height: 1rem;"></i>
                <span>Recouvrer une Créance</span>
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter 
        :currentPeriod="$currentPeriod" 
        :periodLabel="$periodLabel" 
        :statuses="[
            'en_cours' => 'En cours',
            'partiel' => 'Règlement partiel',
            'soldee' => 'Soldée',
            'douteuse' => 'Créance douteuse'
        ]"
    />

    {{-- Cartes de synthèse financière inspirées de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fce4e4; color: #b3261e;">
                        <i data-lucide="alert-octagon" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-danger-pill">Impayés</span>
                </div>
                <div class="font-mono fw-bold text-danger mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ number_format($dettes->sum('reste_a_payer'), 0, ',', ' ') }} <small class="text-muted fs-7">FCFA</small>
                </div>
                <div class="text-muted small">Total Reste à Recouvrer</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Soldées</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $dettes->where('statut', 'soldee')->count() }}
                </div>
                <div class="text-muted small">Total Créances Soldées</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="clock" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-warning-pill">En attente</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $dettes->where('statut', '!=', 'soldee')->count() }}
                </div>
                <div class="text-muted small">Créances En Cours de Recouvrement</div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des créances --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Registre des Impayés & Créances</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="dettesTable" title="Registre des Impayés & Créances" filename="dettes" />
                <span class="badge bg-light text-muted border font-mono">{{ $dettes->count() }} créance(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="dettesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>N° Ticket</th>
                            <th>Patient Débiteur</th>
                            <th class="text-end">Créance Initiale</th>
                            <th class="text-end">Déjà Réglé</th>
                            <th class="text-end">Reste à Recouvrer</th>
                            <th>Date Création</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dettes as $dette)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-mono text-muted">#{{ $dette->id }}</span>
                                </td>
                                <td>
                                    @if($dette->ticket)
                                        <a href="{{ route('tickets.show', $dette->ticket) }}" class="font-mono text-dark fw-semibold text-decoration-none">
                                            #{{ $dette->ticket->reference }}
                                        </a>
                                    @else
                                        <span class="text-muted font-mono">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dette->patient)
                                        <a href="{{ route('patients.show', $dette->patient) }}" class="fw-semibold text-teal text-decoration-none">
                                            {{ $dette->patient->prenom }} {{ $dette->patient->nom }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end font-mono text-dark">{{ number_format($dette->montant_initial, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end text-success fw-semibold font-mono">{{ number_format($dette->montant_paye, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end fw-bold text-danger font-mono fs-6">{{ number_format($dette->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="small text-muted font-mono">
                                        {{ $dette->date_creation ? \Carbon\Carbon::parse($dette->date_creation)->format('d/m/Y') : ($dette->created_at ? $dette->created_at->format('d/m/Y') : '-') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($dette->statut == 'soldee')
                                        <span class="badge badge-success-pill">
                                            Soldée
                                        </span>
                                    @elseif ($dette->statut == 'en_cours')
                                        <span class="badge badge-danger-pill">
                                            En cours
                                        </span>
                                    @else
                                        <span class="badge badge-warning-pill">
                                            {{ ucfirst($dette->statut) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        @if($dette->reste_a_payer > 0 && $dette->ticket_id)
                                            <a href="{{ route('paiements.create', ['ticket_id' => $dette->ticket_id]) }}" class="btn btn-sm btn-teal d-flex align-items-center gap-1 py-1 px-2" title="Régler Créance">
                                                <i data-lucide="banknote" style="width: 0.85rem; height: 0.85rem;"></i>
                                                <span class="small">Régler</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('dettes.show', $dette) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir Détails">
                                            <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('dettes.edit', $dette) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteDetteModal{{ $dette->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteDetteModal{{ $dette->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start p-4">
                                                    Êtes-vous sûr de vouloir supprimer cette créance de <strong class="font-mono text-dark">{{ number_format($dette->reste_a_payer, 0, ',', ' ') }} FCFA</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('dettes.destroy', $dette) }}" method="post" class="d-inline">
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
                                    <i data-lucide="check-circle" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-success"></i>
                                    Aucune créance enregistrée pour ces critères.
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

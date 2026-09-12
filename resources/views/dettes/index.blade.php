@extends('layout')

@section('title', 'Suivi des Dettes & Créances - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-danger font-weight-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Suivi des Dettes & Créances Patients
            </h1>
            <p class="text-muted mb-0">Contrôle des restes à recouvrer et échéances de paiement.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dettes.create') }}" class="btn btn-danger fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Déclarer une Dette
            </a>
            <a href="{{ route('paiements.create') }}" class="btn btn-outline-success fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-wallet2"></i> Recouvrer une Créance
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

    {{-- Cartes de synthèse financière des créances --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-danger">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                        <i class="bi bi-exclamation-octagon fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Reste à Recouvrer</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ number_format($dettes->sum('reste_a_payer'), 0, ',', ' ') }} FBU</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Créances Soldées</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $dettes->where('statut', 'soldee')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Créances En Cours</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ $dettes->where('statut', '!=', 'soldee')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des créances --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-task text-primary me-2"></i>Registre des Impayés & Créances
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="dettesTable" title="Registre des Impayés & Créances" filename="dettes" />
                <span class="badge bg-light text-dark border fs-7">{{ $dettes->count() }} enregistrement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="dettesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>#</th>
                            <th><i class="bi bi-receipt text-muted me-1"></i>N° Ticket</th>
                            <th><i class="bi bi-person text-muted me-1"></i>Patient Débiteur</th>
                            <th class="text-end"><i class="bi bi-cash text-muted me-1"></i>Créance Initiale</th>
                            <th class="text-end"><i class="bi bi-check-lg text-muted me-1"></i>Déjà Réglé</th>
                            <th class="text-end"><i class="bi bi-exclamation-circle text-muted me-1"></i>Reste à Recouvrer</th>
                            <th><i class="bi bi-calendar-event text-muted me-1"></i>Date Création</th>
                            <th class="text-center"><i class="bi bi-flag text-muted me-1"></i>Statut</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dettes as $dette)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-secondary font-monospace">#{{ $dette->id }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-dark font-monospace">
                                        {{ $dette->ticket ? '#' . $dette->ticket->reference : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $dette->patient ? $dette->patient->nom . ' ' . $dette->patient->prenom : 'N/A' }}
                                    </div>
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ number_format($dette->montant_initial, 0, ',', ' ') }} FBU</td>
                                <td class="text-end text-success fw-semibold">{{ number_format($dette->montant_paye, 0, ',', ' ') }} FBU</td>
                                <td class="text-end fw-bold text-danger fs-6">{{ number_format($dette->reste_a_payer, 0, ',', ' ') }} FBU</td>
                                <td>
                                    <small class="text-dark fw-semibold">
                                        {{ $dette->date_creation ? \Carbon\Carbon::parse($dette->date_creation)->format('d/m/Y') : ($dette->created_at ? $dette->created_at->format('d/m/Y') : '-') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if ($dette->statut == 'soldee')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Soldée
                                        </span>
                                    @elseif ($dette->statut == 'en_cours')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> En cours
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                            {{ ucfirst($dette->statut) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        @if($dette->reste_a_payer > 0 && $dette->ticket_id)
                                            <a href="{{ route('paiements.create', ['ticket_id' => $dette->ticket_id]) }}" class="btn btn-outline-success" title="Régler Créance">
                                                <i class="bi bi-wallet2"></i> Régler
                                            </a>
                                        @endif
                                        <a href="{{ route('dettes.show', $dette) }}" class="btn btn-outline-primary" title="Voir Détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('dettes.edit', $dette) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDetteModal{{ $dette->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteDetteModal{{ $dette->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir supprimer cette créance ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-modal="modal">Annuler</button>
                                                    <form action="{{ route('dettes.destroy', $dette) }}" method="post" class="d-inline">
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
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
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

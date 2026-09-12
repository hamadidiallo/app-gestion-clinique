@extends('layout')

@section('title', 'Gestion des Tickets & Factures - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du Module avec Titre, Compteurs et Raccourcis Directs --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-receipt me-2"></i>Tickets de Caisse & Factures
            </h1>
            <p class="text-muted mb-0">Gestion centralisée des admissions, facturations et consultations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Émettre un Nouveau Ticket
            </a>
            <a href="{{ route('paiements.create') }}" class="btn btn-outline-success fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-credit-card"></i> Saisir un Règlement
            </a>
        </div>
    </div>

    {{-- Composant unifié de filtrage de tableau et de période --}}
    <x-period-filter 
        :currentPeriod="$currentPeriod" 
        :periodLabel="$periodLabel" 
        :statuses="['en_attente' => 'En attente de paiement', 'partiellement_paye' => 'Partiellement payé', 'paye' => 'Payé', 'annule' => 'Annulé']" 
    />

    {{-- Cartes de synthèse et indicateurs du module --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-ticket-perforated fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Tickets Émis</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $tickets->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Tickets Payés</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $tickets->where('statut', 'paye')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Paiements Partiels</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ $tickets->whereIn('statut', ['partiel', 'partiellement_paye'])->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-danger">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                        <i class="bi bi-exclamation-octagon fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Impayés / En attente</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ $tickets->whereIn('statut', ['impaye', 'en_attente'])->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau de données moderne avec icônes et badges attractifs --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-stars text-primary me-2"></i>Registre des Tickets
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="ticketsTable" title="Registre des Tickets" filename="tickets" />
                <span class="badge bg-light text-dark border fs-7">{{ $tickets->count() }} enregistrement(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="ticketsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>Référence</th>
                            <th><i class="bi bi-person text-muted me-1"></i>Patient Bénéficiaire</th>
                            <th><i class="bi bi-calendar-event text-muted me-1"></i>Date Émission</th>
                            <th class="text-end"><i class="bi bi-cash text-muted me-1"></i>Total Brut</th>
                            <th class="text-end"><i class="bi bi-shield-check text-muted me-1"></i>Assurance</th>
                            <th class="text-end"><i class="bi bi-person-fill text-muted me-1"></i>Net Patient</th>
                            <th class="text-end"><i class="bi bi-wallet2 text-muted me-1"></i>Reste à Payer</th>
                            <th class="text-center"><i class="bi bi-flag text-muted me-1"></i>Statut</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-dark font-monospace fs-7">#{{ $ticket->reference }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $ticket->patient ? $ticket->patient->nom . ' ' . $ticket->patient->prenom : 'Patient Anonyme' }}
                                    </div>
                                    @php
                                        $rowService = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
                                        $rowMedecin = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
                                    @endphp
                                    @if($rowService || $rowMedecin)
                                        <small class="d-block text-primary">
                                            <i class="bi bi-hospital me-1"></i>{{ $rowService->nom ?? 'Consultation' }}
                                            @if($rowMedecin)
                                                | Dr {{ $rowMedecin->nom }} <span class="badge bg-light text-dark border">{{ $rowMedecin->specialite ?? 'Généraliste' }}</span>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Mat: {{ $ticket->patient->matricule ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block text-dark fw-semibold">
                                        {{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ number_format($ticket->montant_total, 0, ',', ' ') }} FBU</td>
                                <td class="text-end text-success fw-semibold">
                                    @if($ticket->montant_assurance > 0)
                                        <div>-{{ number_format($ticket->montant_assurance, 0, ',', ' ') }} FBU</div>
                                        <small class="badge bg-success-subtle text-success border border-success-subtle">{{ $ticket->assurance->nom ?? 'Assuré' }}</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-primary">{{ number_format($ticket->montant_patient, 0, ',', ' ') }} FBU</td>
                                <td class="text-end fw-bold {{ $ticket->reste_a_payer > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FBU
                                </td>
                                <td class="text-center">
                                    @switch($ticket->statut)
                                        @case('paye')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Payé
                                            </span>
                                            @break
                                        @case('partiel')
                                        @case('partiellement_paye')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                                <i class="bi bi-clock-history me-1"></i> Partiel
                                            </span>
                                            @break
                                        @case('impaye')
                                        @case('en_attente')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i> En attente / Impayé
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($ticket->statut) }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tickets.print', $ticket) }}" target="_blank" class="btn btn-outline-success" title="Imprimer Ticket 80mm">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-primary" title="Voir Détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTicketModal{{ $ticket->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir supprimer définitivement le ticket <strong>#{{ $ticket->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('tickets.destroy', $ticket) }}" method="post" class="d-inline">
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
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Aucun ticket émis correspondant à ces critères.
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

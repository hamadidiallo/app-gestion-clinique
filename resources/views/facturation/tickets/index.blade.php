@extends('layout')

@section('title', 'Gestion des Tickets & Factures - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du Module avec Titre, Compteurs et Raccourcis Directs --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Caisse & Facturation</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Tickets Émis</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">Tickets & Factures</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i data-lucide="plus-circle" class="lucide-sm"></i>
                <span>Nouveau Ticket</span>
            </a>
            <a href="{{ route('paiements.create') }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i data-lucide="credit-card" class="lucide-sm"></i>
                <span>Encaisser Règlement</span>
            </a>
        </div>
    </div>

    {{-- Composant unifié de filtrage de tableau et de période --}}
    <x-period-filter 
        :currentPeriod="$currentPeriod" 
        :periodLabel="$periodLabel" 
        :statuses="['en_attente' => 'En attente de paiement', 'partiellement_paye' => 'Partiellement payé', 'paye' => 'Payé', 'annule' => 'Annulé']" 
    />

    {{-- Cartes de synthèse et indicateurs du module (Style doc/Clinique.dc.html) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="receipt" class="lucide"></i>
                    </div>
                    <span class="badge badge-info-pill">Émis</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $tickets->count() }}</div>
                <div class="small text-muted">Total des tickets émis</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="check-circle-2" class="lucide"></i>
                    </div>
                    <span class="badge badge-success-pill">Réglés</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $tickets->where('statut', 'paye')->count() }}</div>
                <div class="small text-muted">Tickets intégralement payés</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="clock" class="lucide"></i>
                    </div>
                    <span class="badge badge-warning-pill">Partiels</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $tickets->whereIn('statut', ['partiel', 'partiellement_paye'])->count() }}</div>
                <div class="small text-muted">Paiements partiels</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fce4e4; color: #b3261e;">
                        <i data-lucide="alert-circle" class="lucide"></i>
                    </div>
                    <span class="badge badge-danger-pill">Attente</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $tickets->whereIn('statut', ['impaye', 'en_attente'])->count() }}</div>
                <div class="small text-muted">En attente d'encaissement</div>
            </div>
        </div>
    </div>

    {{-- Tableau de données moderne avec icônes Lucide --}}
    <div class="card overflow-hidden">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="receipt-text" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Registre des Tickets & Prestations</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="ticketsTable" title="Registre des Tickets" filename="tickets" />
                <span class="badge bg-light text-muted border font-mono">{{ $tickets->count() }} ticket(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="ticketsTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 110px;">Référence</th>
                            <th>Patient Bénéficiaire</th>
                            <th>Date Émission</th>
                            <th class="text-end">Total Brut</th>
                            <th class="text-end">Part Tiers</th>
                            <th class="text-end">Net Patient</th>
                            <th class="text-end">Reste Dû</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-3">
                                    <span class="font-mono text-muted small">#{{ $ticket->reference }}</span>
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
                                        <small class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.76rem;">
                                            <span>{{ $rowService->nom ?? 'Consultation' }}</span>
                                            @if($rowMedecin)
                                                <span>·</span>
                                                <span class="text-dark fw-semibold">Dr {{ $rowMedecin->nom }}</span>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted font-mono" style="font-size: 0.74rem;">Mat: {{ $ticket->patient->matricule ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono small text-muted">
                                        {{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y H:i') : '—' }}
                                    </span>
                                </td>
                                <td class="text-end font-mono text-muted">{{ number_format($ticket->montant_total, 0, ',', ' ') }} F</td>
                                <td class="text-end font-mono" style="color: #0f6b5f;">
                                    @if($ticket->montant_assurance > 0)
                                        <div>-{{ number_format($ticket->montant_assurance, 0, ',', ' ') }} F</div>
                                        <small class="badge badge-success-pill" style="font-size: 0.65rem;">{{ $ticket->assurance->nom ?? 'Assuré' }}</small>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end font-mono fw-bold text-dark">{{ number_format($ticket->montant_patient, 0, ',', ' ') }} F</td>
                                <td class="text-end font-mono fw-bold {{ $ticket->reste_a_payer > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} F
                                </td>
                                <td class="text-center">
                                    @switch($ticket->statut)
                                        @case('paye')
                                            <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #12a594; display: inline-block;"></span>
                                                <span>Payé</span>
                                            </span>
                                            @break
                                        @case('partiel')
                                        @case('partiellement_paye')
                                            <span class="badge badge-warning-pill d-inline-flex align-items-center gap-1">
                                                <span>Partiel</span>
                                            </span>
                                            @break
                                        @case('impaye')
                                        @case('en_attente')
                                            <span class="badge badge-danger-pill d-inline-flex align-items-center gap-1">
                                                <span>En attente</span>
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-muted border">{{ ucfirst($ticket->statut) }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tickets.print', $ticket) }}" target="_blank" class="btn btn-outline-secondary" title="Imprimer Ticket 80mm">
                                            <i data-lucide="printer" class="lucide-sm"></i>
                                        </a>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" title="Voir Détails">
                                            <i data-lucide="eye" class="lucide-sm"></i>
                                            <span>Fiche</span>
                                        </a>
                                        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i data-lucide="edit-3" class="lucide-sm"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTicketModal{{ $ticket->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" class="lucide-sm"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de confirmation de suppression --}}
                                    <div class="modal fade" id="deleteTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white py-2">
                                                    <h6 class="modal-title d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" class="lucide-sm"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start py-4">
                                                    Êtes-vous certain de vouloir supprimer définitivement le ticket <strong>#{{ $ticket->reference }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('tickets.destroy', $ticket) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer définitivement</button>
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
                                    <i data-lucide="receipt" class="lucide-lg d-block mx-auto mb-2 text-muted opacity-50"></i>
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

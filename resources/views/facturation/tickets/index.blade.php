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
                <style>
                    .table-registre thead th {
                        background: #f6f9f8;
                        color: #5b7169;
                        font-size: .7rem;
                        font-weight: 700;
                        letter-spacing: .06em;
                        text-transform: uppercase;
                        white-space: nowrap;
                        padding-top: .85rem;
                        padding-bottom: .85rem;
                        border-bottom: 1px solid #e4ede9;
                    }
                    .table-registre tbody td { padding-top: .9rem; padding-bottom: .9rem; border-color: #eef3f1; }
                    .table-registre tbody tr:hover { background: #f8fbfa; }
                    .table-registre tfoot td { background: #f6f9f8; border-top: 2px solid #e4ede9; padding-top: .85rem; padding-bottom: .85rem; }
                    .table-registre .dropdown-item { font-size: .875rem; padding: .5rem .9rem; }
                    .table-registre .devise { font-size: .72em; font-weight: 600; opacity: .7; margin-left: .2em; }
                </style>
                @php
                    // Devise réglée dans les paramètres de la clinique (FCFA par défaut)
                    $devise = auth()->user()?->clinique?->devise ?: 'FCFA';
                @endphp
                <table id="ticketsTable" class="table table-hover align-middle mb-0 table-registre">
                    <thead>
                        <tr>
                            <th class="ps-4">Ticket</th>
                            <th>Patient</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Part assurance</th>
                            <th class="text-end">Net patient</th>
                            <th class="text-end">Reste dû</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-4 no-export">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            @php
                                $rowService = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
                                $rowMedecin = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
                                $dateTicket = $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket) : null;
                                $assuranceCourte = $ticket->assurance ? \Illuminate\Support\Str::before($ticket->assurance->nom, ' (') : null;
                            @endphp
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-mono fw-semibold text-teal text-decoration-none small">{{ $ticket->reference }}</a>
                                    <div class="small text-muted font-mono">
                                        {{ $dateTicket?->format('d/m/Y') ?? '—' }}
                                        <span class="opacity-75">{{ $dateTicket?->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td style="min-width: 190px;">
                                    <div class="fw-bold text-dark text-nowrap">
                                        @if($ticket->patient)
                                            {{ mb_strtoupper($ticket->patient->nom) }} <span class="fw-semibold">{{ $ticket->patient->prenom }}</span>
                                        @else
                                            Patient anonyme
                                        @endif
                                    </div>
                                    <div class="small text-muted text-truncate" style="max-width: 260px;">
                                        {{ $rowService->nom ?? 'Consultation' }}@if($rowMedecin) · Dr {{ $rowMedecin->nom }}@endif
                                    </div>
                                </td>
                                <td class="text-end font-mono text-nowrap text-muted">{{ number_format($ticket->montant_total, 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td class="text-end text-nowrap">
                                    @if($ticket->montant_assurance > 0)
                                        <div class="font-mono" style="color: #0f6b5f;">−{{ number_format($ticket->montant_assurance, 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></div>
                                        <div class="small text-muted" title="{{ $ticket->assurance->nom ?? '' }}">{{ $assuranceCourte ?? 'Assuré' }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end font-mono fw-bold text-dark text-nowrap">{{ number_format($ticket->montant_patient, 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td class="text-end font-mono fw-bold text-nowrap {{ $ticket->reste_a_payer > 0 ? 'text-danger' : 'text-muted opacity-50' }}">
                                    {{ number_format($ticket->reste_a_payer, 0, ',', ' ') }}<span class="devise">{{ $devise }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @switch($ticket->statut)
                                        @case('paye')
                                            <span class="badge badge-success-pill">Payé</span>
                                            @break
                                        @case('partiel')
                                        @case('partiellement_paye')
                                            <span class="badge badge-warning-pill">Partiel</span>
                                            @break
                                        @case('impaye')
                                        @case('en_attente')
                                            <span class="badge badge-danger-pill">En attente</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-muted border">{{ ucfirst($ticket->statut) }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-light border fw-semibold">Fiche</a>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-light border px-2" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" aria-label="Autres actions pour le ticket {{ $ticket->reference }}">
                                                <i data-lucide="more-horizontal" class="lucide-sm"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 210px;">
                                                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('tickets.print', $ticket) }}" target="_blank"><i data-lucide="printer" class="lucide-sm"></i> Imprimer le ticket 80 mm</a></li>
                                                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('tickets.facture', $ticket) }}" target="_blank"><i data-lucide="file-text" class="lucide-sm"></i> Facture A4</a></li>
                                                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('tickets.edit', $ticket) }}"><i data-lucide="edit-3" class="lucide-sm"></i> Modifier</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteTicketModal{{ $ticket->id }}"><i data-lucide="trash-2" class="lucide-sm"></i> Supprimer</button></li>
                                            </ul>
                                        </div>
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
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body text-start py-4" style="white-space: normal;">
                                                    Êtes-vous certain de vouloir supprimer définitivement le ticket <strong>{{ $ticket->reference }}</strong> ?
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i data-lucide="receipt" class="lucide-lg d-block mx-auto mb-2 text-muted opacity-50"></i>
                                    Aucun ticket émis correspondant à ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($tickets->isNotEmpty())
                        <tfoot>
                            <tr class="no-export">
                                <td class="ps-4 fw-bold" colspan="2">Total · {{ $tickets->count() }} ticket(s)</td>
                                <td class="text-end font-mono fw-bold text-nowrap">{{ number_format($tickets->sum('montant_total'), 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td class="text-end font-mono fw-bold text-nowrap" style="color: #0f6b5f;">−{{ number_format($tickets->sum('montant_assurance'), 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td class="text-end font-mono fw-bold text-nowrap">{{ number_format($tickets->sum('montant_patient'), 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td class="text-end font-mono fw-bold text-nowrap {{ $tickets->sum('reste_a_payer') > 0 ? 'text-danger' : 'text-muted' }}">{{ number_format($tickets->sum('reste_a_payer'), 0, ',', ' ') }}<span class="devise">{{ $devise }}</span></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

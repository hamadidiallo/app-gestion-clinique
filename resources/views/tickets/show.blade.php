@extends('layout')

@section('title', 'Détails du Ticket : ' . $ticket->reference)

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec boutons d'actions directes --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="btn btn-light border bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour aux tickets">
                <i data-lucide="arrow-left" style="width: 1.15rem; height: 1.15rem;"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h3 text-dark fw-bold mb-0">Ticket N° <span class="font-mono text-teal">{{ $ticket->reference }}</span></h1>
                    @if($ticket->statut === 'paye')
                        <span class="badge badge-success-pill px-2.5 py-1">Payé</span>
                    @elseif($ticket->statut === 'partiellement_paye')
                        <span class="badge badge-warning-pill px-2.5 py-1">Partiellement payé</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">En attente</span>
                    @endif
                </div>
                <small class="text-muted">Reçu d'encaissement et justificatif d'actes médicaux &bull; Émis le <span class="font-mono">{{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y H:i') : '-' }}</span></small>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if($ticket->consultation)
                <a href="{{ route('consultations.show', $ticket->consultation) }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-1">
                    <i data-lucide="heart-pulse" style="width: 1rem; height: 1rem;"></i>
                    <span>Voir Consultation</span>
                </a>
            @else
                <a href="{{ route('consultations.create', ['ticket_id' => $ticket->id]) }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-1">
                    <i data-lucide="heart-pulse" style="width: 1rem; height: 1rem;"></i>
                    <span>+ Ouvrir Consultation</span>
                </a>
            @endif
            <a href="{{ route('tickets.print', $ticket) }}" target="_blank" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-1">
                <i data-lucide="printer" style="width: 1rem; height: 1rem;"></i>
                <span>Imprimer Reçu (80mm)</span>
            </a>
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Modifier le ticket">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- COLONNE GAUCHE: Informations Générales & Patient --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="info" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Informations Administratives</span>
                    </h5>
                    <span class="font-mono small text-muted">REF: {{ $ticket->reference }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Patient Bénéficiaire</span>
                            @if($ticket->patient)
                                <a href="{{ route('patients.show', $ticket->patient) }}" class="fs-6 fw-bold text-teal text-decoration-none d-block">
                                    {{ $ticket->patient->prenom }} {{ $ticket->patient->nom }}
                                </a>
                                <small class="text-muted font-mono">Sexe: {{ $ticket->patient->sexe ?? '-' }} | Mat: {{ $ticket->patient->matricule ?? 'N/A' }}</small>
                            @else
                                <strong class="fs-6 text-dark">N/A</strong>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Prise en Charge / Assurance</span>
                            @if($ticket->assurance)
                                <span class="badge badge-success-pill d-inline-flex align-items-center gap-1 font-mono">
                                    <i data-lucide="shield-check" style="width: 0.85rem; height: 0.85rem;"></i>
                                    <span>{{ $ticket->assurance->nom }}</span>
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Paiement Direct (Sans Assurance)</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Service Médical</span>
                            @php
                                $serviceObj = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
                            @endphp
                            <strong class="fs-6 text-dark">{{ $serviceObj->nom ?? 'Consultation générale' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Médecin Traitant</span>
                            @php
                                $medecinObj = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
                            @endphp
                            @if($medecinObj)
                                <strong class="fs-6 text-dark">Dr {{ $medecinObj->nom }} {{ $medecinObj->prenom }}</strong>
                                <small class="d-block text-teal">{{ $medecinObj->specialite ?? 'Généraliste' }}</small>
                            @else
                                <span class="text-muted fs-6">Non spécifié</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Date d'Émission</span>
                            <strong class="text-dark font-mono">{{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y à H:i') : '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1">Validité</span>
                            <strong class="text-danger font-mono">{{ $ticket->date_expiration ? \Carbon\Carbon::parse($ticket->date_expiration)->format('d/m/Y') : '7 Jours' }}</strong>
                        </div>
                        <div class="col-md-12">
                            <span class="text-muted small d-block mb-1">Caissier / Agent Émetteur</span>
                            <strong class="text-dark">{{ $ticket->user ? $ticket->user->name : 'Guichetier' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tableau des prestations et détails du ticket --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="list-checks" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Prestations & Articles Facturés</span>
                    </h5>
                    <span class="badge bg-teal rounded-pill font-mono">{{ $ticket->details->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Désignation</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix U.</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticket->details as $detail)
                                    <tr>
                                        <td>
                                            @if(($detail->type_item ?? '') === 'medicament')
                                                <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                                    <i data-lucide="pill" style="width: 0.8rem; height: 0.8rem;"></i>
                                                    <span>Médicament</span>
                                                </span>
                                            @elseif(($detail->type_item ?? '') === 'hospitalisation')
                                                <span class="badge badge-info-pill d-inline-flex align-items-center gap-1">
                                                    <i data-lucide="bed" style="width: 0.8rem; height: 0.8rem;"></i>
                                                    <span>Hospitalisation</span>
                                                </span>
                                            @else
                                                <span class="badge badge-info-pill d-inline-flex align-items-center gap-1">
                                                    <i data-lucide="activity" style="width: 0.8rem; height: 0.8rem;"></i>
                                                    <span>Acte Médical</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-dark">{{ $detail->libelle }}</strong>
                                        </td>
                                        <td class="text-center fw-bold font-mono">{{ number_format($detail->quantite, 0) }}</td>
                                        <td class="text-end font-mono">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end fw-bold text-dark font-mono pe-3">{{ number_format($detail->montant_total, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aucun détail enregistré pour ce ticket.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE: Totaux Financiers & Paiements --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title text-dark fw-bold mb-0 fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="wallet" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Synthèse Financière</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Total Brut des Prestations :</span>
                        <span class="fw-bold text-dark font-mono">{{ number_format($ticket->montant_total, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Prise en Charge Assurance :</span>
                        <span class="fw-bold text-teal font-mono">- {{ number_format($ticket->montant_assurance, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-6 text-dark">Net à Payer (Patient) :</span>
                        <span class="fw-bold fs-5 text-teal font-mono">{{ number_format($ticket->montant_patient, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Montant Déjà Encaissé :</span>
                        <span class="fw-bold text-success font-mono">{{ number_format($ticket->montant_paye, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center" style="background: {{ $ticket->reste_a_payer > 0 ? '#fce4e4' : '#e3f3ee' }};">
                        <span class="fw-bold {{ $ticket->reste_a_payer > 0 ? 'text-danger' : 'text-teal' }}">Solde Restant à Payer :</span>
                        <span class="fw-bold fs-4 font-mono {{ $ticket->reste_a_payer > 0 ? 'text-danger' : 'text-teal' }}">{{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if($ticket->reste_a_payer > 0)
                        <a href="{{ route('paiements.create', ['ticket_id' => $ticket->id]) }}" class="btn btn-teal w-100 py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="banknote" style="width: 1.1rem; height: 1.1rem;"></i>
                            <span>Enregistrer un Règlement</span>
                        </a>
                    @else
                        <div class="p-2.5 rounded-3 text-center fw-semibold d-flex align-items-center justify-content-center gap-2" style="background: #e3f3ee; color: #0f6b5f;">
                            <i data-lucide="check-circle" style="width: 1.1rem; height: 1.1rem;"></i>
                            <span>Ticket Entièrement Soldé</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

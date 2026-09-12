@extends('layout')

@section('title', 'Détails du Ticket de Consultation')

@section('content')
<div class="container py-4">
    {{-- En-tête avec boutons d'actions directes (Impression Ticket Thermique 80mm & PDF) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">🧾 Ticket N° <code>{{ $ticket->reference }}</code></h1>
            <p class="text-muted mb-0">Reçu d'encaissement et justificatif de consultation médicale.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Bouton d'impression ticket thermique 80mm --}}
            <a href="{{ route('tickets.print', $ticket) }}" target="_blank" class="btn btn-success fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Imprimer Ticket (80mm)
            </a>
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Modifier
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour à la Liste
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- COLONNE GAUCHE: Informations Générales & Patient --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informations de la Consultation</h5>
                    <span class="badge bg-light text-primary font-weight-bold fs-6">{{ strtoupper($ticket->statut) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Patient Beneficiaire</span>
                            <strong class="fs-6 text-dark">{{ $ticket->patient ? $ticket->patient->nom . ' ' . $ticket->patient->prenom : 'N/A' }}</strong>
                            <small class="d-block text-muted">Sexe: {{ $ticket->patient->sexe ?? '-' }} | Mat: {{ $ticket->patient->matricule ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Organisme d'Assurance</span>
                            <strong class="fs-6 text-primary">{{ $ticket->assurance ? $ticket->assurance->nom : 'Paiement Direct (Sans Assurance)' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block"><i class="bi bi-hospital text-primary me-1"></i>Service Médical</span>
                            @php
                                $serviceObj = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
                            @endphp
                            <strong class="fs-6 text-dark">{{ $serviceObj->nom ?? 'Consultation générale' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block"><i class="bi bi-person-heart text-primary me-1"></i>Médecin Traitant & Spécialité</span>
                            @php
                                $medecinObj = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
                            @endphp
                            @if($medecinObj)
                                <strong class="fs-6 text-dark">Dr {{ $medecinObj->nom }} {{ $medecinObj->prenom }}</strong>
                                <small class="d-block text-primary fw-semibold"><i class="bi bi-award me-1"></i>Spécialité : {{ $medecinObj->specialite ?? 'Généraliste' }}</small>
                            @else
                                <span class="text-muted fs-6">Non spécifié</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Date d'Émission du Ticket</span>
                            <strong class="text-dark">{{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y H:i') : '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Date Limite de Validité (7 Jours)</span>
                            <strong class="text-danger">{{ $ticket->date_expiration ? \Carbon\Carbon::parse($ticket->date_expiration)->format('d/m/Y H:i') : 'Valable 7 jours' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Caissier / Agent Émetteur</span>
                            <strong class="text-dark">{{ $ticket->user ? $ticket->user->name : 'Guichetier' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tableau des actes et détails du ticket --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-dark font-weight-bold"><i class="bi bi-list-check me-2"></i>Prestations Médicales Incluses</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Prestation / Acte</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix U. (FBU)</th>
                                    <th class="text-center">Taux Ass.</th>
                                    <th class="text-end">Part Patient</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticket->details as $detail)
                                    <tr>
                                        <td class="fw-bold">{{ $detail->prestation->libelle ?? 'Acte Médical' }}</td>
                                        <td class="text-center">{{ $detail->quantite }}</td>
                                        <td class="text-end">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }}</td>
                                        <td class="text-center"><span class="badge bg-info text-dark">{{ number_format($detail->taux_couverture_applique, 0) }}%</span></td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($detail->montant_part_patient, 0, ',', ' ') }} FBU</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Aucun détail enregistré pour ce ticket.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE: Totaux Financiers & Paiements --}}
        <div class="col-md-5">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="card-title text-dark font-weight-bold mb-3">💰 Synthèse Financière du Ticket</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Tarif Public (Brut):</span>
                        <span class="fw-bold text-dark">{{ number_format($ticket->montant_total, 0, ',', ' ') }} FBU</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prise en Charge Assurance:</span>
                        <span class="fw-bold text-success">- {{ number_format($ticket->montant_assurance, 0, ',', ' ') }} FBU</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold fs-6 text-dark">Net à Payer (Patient):</span>
                        <span class="fw-bold fs-5 text-primary">{{ number_format($ticket->montant_patient, 0, ',', ' ') }} FBU</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Montant Déjà Encaissé:</span>
                        <span class="fw-bold text-success">{{ number_format($ticket->montant_paye, 0, ',', ' ') }} FBU</span>
                    </div>

                    <div class="p-3 rounded bg-white border d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-danger">Solde Restant à Payer:</span>
                        <span class="fw-bold fs-4 text-danger">{{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FBU</span>
                    </div>

                    @if($ticket->reste_a_payer > 0)
                        <a href="{{ route('paiements.create', ['ticket_id' => $ticket->id]) }}" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-wallet2 me-1"></i> Enregistrer un Règlement
                        </a>
                    @else
                        <div class="alert alert-success mb-0 text-center fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Ticket Entièrement Réglé
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

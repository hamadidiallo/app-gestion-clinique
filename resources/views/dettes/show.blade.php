@extends('layout')

@section('title', 'Dossier de Créance #' . $dette->id . ' - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane --}}
    <div class="small text-muted mb-2 d-flex align-items-center gap-1">
        <span>Caisse & Facturation</span>
        <span class="opacity-50">/</span>
        <a href="{{ route('dettes.index') }}" class="text-decoration-none text-muted">Dettes & Impayés</a>
        <span class="opacity-50">/</span>
        <span class="fw-semibold text-dark">Dossier de Créance #{{ $dette->id }}</span>
    </div>

    {{-- BANNIÈRE HERO DU DOSSIER DE CRÉANCE --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
        <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                     style="width: 64px; height: 64px; font-size: 1.4rem; background: {{ $dette->reste_a_payer > 0 ? 'linear-gradient(135deg, #dc2626, #f87171)' : 'linear-gradient(135deg, #059669, #34d399)' }}; flex-shrink: 0;">
                    <i data-lucide="{{ $dette->reste_a_payer > 0 ? 'alert-triangle' : 'check-circle' }}" class="lucide-md"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h3 text-dark fw-bold mb-0">Créance Patient #{{ $dette->id }}</h1>
                        @if($dette->reste_a_payer <= 0 || $dette->statut === 'soldee')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold rounded-pill">
                                🟢 Créance Soldée
                            </span>
                        @elseif($dette->statut === 'partiel' || $dette->montant_paye > 0)
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold rounded-pill">
                                🟡 Recouvrement Partiel
                            </span>
                        @elseif($dette->statut === 'douteuse')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold rounded-pill">
                                ⚠️ Créance Douteuse
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-bold rounded-pill">
                                🔴 Non Réglé (En cours)
                            </span>
                        @endif

                        @if($dette->ticket)
                            <a href="{{ route('tickets.show', $dette->ticket) }}" class="badge bg-light text-dark border text-decoration-none px-2 py-1 font-mono">
                                Ticket #{{ $dette->ticket->reference }}
                            </a>
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        Patient débiteur : <strong class="text-dark">{{ $dette->patient ? $dette->patient->nom . ' ' . $dette->patient->prenom : 'Anonyme' }}</strong>
                        @if($dette->patient && $dette->patient->telephone)
                            • Tél : <a href="tel:{{ $dette->patient->telephone }}" class="text-decoration-none font-mono text-dark fw-bold">{{ $dette->patient->telephone }}</a>
                        @endif
                        • Enregistré le {{ $dette->date_creation ? $dette->date_creation->format('d/m/Y') : $dette->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            {{-- Boutons d'actions rapides --}}
            <div class="d-flex flex-wrap gap-2">
                @if($dette->reste_a_payer > 0)
                    <a href="{{ route('paiements.create', ['ticket_id' => $dette->ticket_id]) }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-3 fw-bold" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                        <i data-lucide="hand-coins" class="lucide-sm"></i>
                        <span>Encaisser / Solder la Dette</span>
                    </a>
                @endif
                <a href="{{ route('dettes.edit', $dette) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i data-lucide="calendar" class="lucide-sm"></i>
                    <span>Modifier l'échéance</span>
                </a>
                <a href="{{ route('dettes.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" class="lucide-sm"></i>
                    <span>Retour</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 3 CARTES KPI FINANCIÈRES --}}
    <div class="row g-3 mb-4">
        {{-- Créance Initiale --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Créance Initiale</span>
                        <h3 class="h3 font-mono fw-bold text-dark mb-0 mt-1">
                            {{ number_format($dette->montant_initial, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                        </h3>
                        <span class="extra-small text-muted">Montant restant dû à la facturation</span>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-muted">
                        <i data-lucide="receipt" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Montant Déjà Réglé --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Montant Encaissé</span>
                        <h3 class="h3 font-mono fw-bold text-success mb-0 mt-1">
                            {{ number_format($dette->montant_paye, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                        </h3>
                        <span class="extra-small text-success">Versements déjà perçus</span>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success">
                        <i data-lucide="check" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reste à Recouvrer --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100" style="border-left: 4px solid {{ $dette->reste_a_payer > 0 ? '#dc2626' : '#059669' }} !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Reste à Recouvrer</span>
                        <h3 class="h3 font-mono fw-bold {{ $dette->reste_a_payer > 0 ? 'text-danger' : 'text-success' }} mb-0 mt-1">
                            {{ number_format($dette->reste_a_payer, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                        </h3>
                        <span class="extra-small {{ $dette->reste_a_payer > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $dette->reste_a_payer > 0 ? 'Solde débiteur à percevoir' : 'Dette intégralement apurée' }}
                        </span>
                    </div>
                    <div class="rounded-3 p-2 {{ $dette->reste_a_payer > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                        <i data-lucide="{{ $dette->reste_a_payer > 0 ? 'wallet' : 'award' }}" class="lucide-md"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DÉTAILS DU DOSSIER : 2 COLONNES --}}
    <div class="row g-4">
        {{-- COLONNE GAUCHE : COORDONNÉES PATIENT & MODALITÉS --}}
        <div class="col-lg-5">
            {{-- Fiche Débiteur --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="user" class="lucide-xs"></i>
                    </span>
                    Informations du Débiteur
                </h5>

                @if($dette->patient)
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded-3 bg-light border">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #0f766e; flex-shrink: 0;">
                            {{ strtoupper(substr($dette->patient->nom, 0, 1) . substr($dette->patient->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('patients.show', $dette->patient) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $dette->patient->nom }} {{ $dette->patient->prenom }}
                            </a>
                            <span class="text-muted extra-small">Matricule : {{ $dette->patient->matricule ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Numéro de Téléphone :</span>
                            @if($dette->patient->telephone)
                                <a href="tel:{{ $dette->patient->telephone }}" class="font-mono fw-bold text-primary text-decoration-none">
                                    <i data-lucide="phone-call" class="lucide-xs me-1"></i> {{ $dette->patient->telephone }}
                                </a>
                            @else
                                <span class="text-muted">Non renseigné</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Adresse / Résidence :</span>
                            <span class="text-dark">{{ $dette->patient->adresse ?? 'Non précisée' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted">Prise en Charge :</span>
                            @if($dette->patient->assurance)
                                <span class="badge bg-info-subtle text-info fw-bold">{{ $dette->patient->assurance->nom }} ({{ $dette->patient->taux_couverture }}%)</span>
                            @else
                                <span class="badge bg-light text-muted border">Patient Privé</span>
                            @endif
                        </li>
                    </ul>
                @else
                    <p class="text-muted small">Aucun profil patient rattaché.</p>
                @endif
            </div>

            {{-- Modalités & Échéance --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: #e0f2fe; color: #0284c7;">
                        <i data-lucide="clock" class="lucide-xs"></i>
                    </span>
                    Modalités & Suivi
                </h5>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Date de Survenue :</span>
                        <strong class="font-mono text-dark">{{ $dette->date_creation ? $dette->date_creation->format('d/m/Y') : '--' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Date d'Échéance Prévue :</span>
                        @if($dette->date_reglement)
                            <strong class="font-mono text-dark">{{ $dette->date_reglement->format('d/m/Y') }}</strong>
                        @else
                            <span class="text-muted font-italic">Non définie</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Agent Responsable :</span>
                        <span class="text-dark fw-semibold">{{ $dette->user->name ?? 'Système' }}</span>
                    </li>
                </ul>

                <div class="mt-3">
                    <span class="text-muted small fw-bold d-block mb-1">Motif / Observations :</span>
                    <div class="p-2.5 rounded-3 bg-light border small text-muted">
                        {{ $dette->description ?? 'Reste à payer sur ticket de consultation / soins.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : PRESTATIONS DU TICKET & HISTORIQUE DES RÈGLEMENTS --}}
        <div class="col-lg-7">
            {{-- Ce que le patient a consommé (Détails du ticket) --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="list-checks" class="lucide-sm text-primary"></i>
                        <span>Prestations & Soins Facturés (Origine de la Dette)</span>
                    </h5>
                    @if($dette->ticket)
                        <a href="{{ route('tickets.show', $dette->ticket) }}" class="btn btn-xs btn-outline-primary py-1 px-2">
                            <i data-lucide="external-link" class="lucide-xs"></i> Voir Ticket Complet
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($dette->ticket && $dette->ticket->details && $dette->ticket->details->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Prestation / Soin</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-end">Prix U.</th>
                                        <th class="text-end pe-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dette->ticket->details as $detail)
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $detail->libelle }}</strong>
                                                <span class="badge bg-light text-muted border extra-small ms-1">{{ ucfirst($detail->type_item ?? 'acte') }}</span>
                                            </td>
                                            <td class="text-center font-mono">{{ number_format($detail->quantite, 0) }}</td>
                                            <td class="text-end font-mono">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end font-mono fw-bold text-dark pe-3">{{ number_format($detail->montant_total, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">
                            Ticket #{{ $dette->ticket->reference ?? 'N/A' }} — Détail non détaillé ou forfaitaire.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Historique des Règlements Encaissés --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="history" class="lucide-sm text-success"></i>
                        <span>Historique des Versements Reçus</span>
                    </h5>
                    @if($dette->reste_a_payer > 0)
                        <a href="{{ route('paiements.create', ['ticket_id' => $dette->ticket_id]) }}" class="btn btn-xs btn-outline-success py-1 px-2 fw-bold">
                            + Nouveau Versement
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @php
                        $paiements = $dette->ticket && $dette->ticket->paiements ? $dette->ticket->paiements : collect();
                    @endphp

                    @if($paiements->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            <i data-lucide="alert-circle" class="lucide-sm mb-1 opacity-50"></i>
                            <p class="mb-2">Aucun paiement ou acompte n'a été enregistré sur cette créance.</p>
                            @if($dette->reste_a_payer > 0)
                                <a href="{{ route('paiements.create', ['ticket_id' => $dette->ticket_id]) }}" class="btn btn-sm btn-primary">
                                    Enregistrer le premier règlement ({{ number_format($dette->reste_a_payer, 0, ',', ' ') }} FCFA)
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reçu N°</th>
                                        <th>Date & Heure</th>
                                        <th>Mode</th>
                                        <th class="text-end">Montant Encaissé</th>
                                        <th class="text-center pe-3">Reçu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paiements as $p)
                                        <tr>
                                            <td class="font-mono fw-bold text-dark">#{{ $p->reference }}</td>
                                            <td class="font-mono text-muted">{{ $p->date_paiement ? $p->date_paiement->format('d/m/Y H:i') : $p->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $p->modePaiement->nom ?? 'Espèces' }}</span>
                                            </td>
                                            <td class="text-end font-mono fw-bold text-success">
                                                {{ number_format($p->montant_impute, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-center pe-3">
                                                <a href="{{ route('paiements.print', $p) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 small">
                                                    <i data-lucide="printer" class="lucide-xs"></i> Imprimer
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
<style>
.extra-small { font-size: 0.78rem; }
</style>
@endpush

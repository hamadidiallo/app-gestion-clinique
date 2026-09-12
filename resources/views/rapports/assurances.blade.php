@extends('layout')

@section('title', 'Relevé Détaillé Assurances (Tiers Payant) - CLINGEST')

@section('content')
<div class="container py-4">
    {{-- En-tête avec bouton retour --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold">🛡️ Relevé Détaillé des Prestations Prises en Charge (Tiers Payant)</h1>
            <p class="text-muted mb-0">Justificatif officiel d'encaissement et de recouvrement à destination des compagnies d'assurances.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('rapports.index') }}" class="btn btn-outline-secondary">
                ⬅ Retour aux Rapports
            </a>
            <x-export-buttons table-id="rapportAssurancesTable" title="Relevé Détaillé Tiers Payant Assurances" filename="releve_tiers_payant_assurances" />
        </div>
    </div>

    {{-- Formulaire de filtres analytiques par assurance et par plage de dates --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('rapports.assurances') }}" class="row g-3 align-items-end">
                {{-- Sélection de l'organisme d'assurance --}}
                <div class="col-md-4">
                    <label for="assurance_id" class="form-label font-weight-bold">Organisme d'Assurance :</label>
                    <select name="assurance_id" id="assurance_id" class="form-select">
                        <option value="">-- Toutes les Assurances --</option>
                        @foreach($assurances as $assurance)
                            <option value="{{ $assurance->id }}" {{ $assuranceId == $assurance->id ? 'selected' : '' }}>
                                {{ $assurance->nom }} (Mat: {{ $assurance->code ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date de début de période --}}
                <div class="col-md-3">
                    <label for="date_debut" class="form-label font-weight-bold">Date Début :</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ $dateDebut }}">
                </div>

                {{-- Date de fin de période --}}
                <div class="col-md-3">
                    <label for="date_fin" class="form-label font-weight-bold">Date Fin :</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ $dateFin }}">
                </div>

                {{-- Boutons d'action --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    <a href="{{ route('rapports.assurances') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Synthèse des montants globaux pour l'assurance sélectionnée --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-start border-4 border-secondary">
                <span class="text-muted small fw-semibold">Total Tarif Prestations (Public)</span>
                <h4 class="text-dark font-weight-bold mb-0">{{ number_format($totalTarifPublic, 0, ',', ' ') }} FBU</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-start border-4 border-success">
                <span class="text-muted small fw-semibold">Total Payé par les Assurés (Patients)</span>
                <h4 class="text-success font-weight-bold mb-0">{{ number_format($totalPartPatient, 0, ',', ' ') }} FBU</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-start border-4 border-primary">
                <span class="text-muted small fw-semibold">Total Part Due par l'Assurance</span>
                <h4 class="text-primary font-weight-bold mb-0">{{ number_format($totalPartAssurance, 0, ',', ' ') }} FBU</h4>
            </div>
        </div>
    </div>

    {{-- Tableau justificatif détaillé ligne par ligne conforme au cahier des charges Tiers Payant --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="rapportAssurancesTable" class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Ticket / Réf</th>
                            <th>Date Visite</th>
                            <th>Patient (Nom & Prénom)</th>
                            <th>Assurance</th>
                            <th>Prestation / Service</th>
                            <th class="text-end">Tarif Prestation</th>
                            <th class="text-center">Taux (%)</th>
                            <th class="text-end text-success">Part Assuré (Payé)</th>
                            <th class="text-end text-primary">Part Assurance (Due)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $detail)
                            @php
                                $tarifLigne = $detail->montant_total ?? ($detail->prix_unitaire * $detail->quantite);
                                $tauxLigne = $detail->taux_couverture ?? 0;
                                $partPatientLigne = $detail->montant_patient ?? 0;
                                $partAssuranceLigne = $detail->montant_assurance ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-secondary font-monospace">#{{ $detail->ticket->reference ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small>{{ $detail->ticket->date_ticket ? \Carbon\Carbon::parse($detail->ticket->date_ticket)->format('d/m/Y H:i') : '-' }}</small>
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $detail->ticket->patient->nom ?? 'N/A' }} {{ $detail->ticket->patient->prenom ?? '' }}
                                    <small class="d-block text-muted">Mat: {{ $detail->ticket->patient->matricule ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        {{ $detail->ticket->assurance->nom ?? 'Paiement Direct' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $detail->prestation->service->nom ?? $detail->prestation->type ?? 'Consultation Médicale' }}
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($tarifLigne, 0, ',', ' ') }} FBU</td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                        {{ number_format($tauxLigne, 0) }}%
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($partPatientLigne, 0, ',', ' ') }} FBU
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    {{ number_format($partAssuranceLigne, 0, ',', ' ') }} FBU
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Aucune prise en charge d'assurance (Tiers Payant) trouvée pour cette sélection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end fs-6 text-dark">TOTAUX DU RELEVÉ TIERS PAYANT :</td>
                            <td class="text-end text-dark fs-6">{{ number_format($totalTarifPublic, 0, ',', ' ') }} FBU</td>
                            <td></td>
                            <td class="text-end text-success fs-6">{{ number_format($totalPartPatient, 0, ',', ' ') }} FBU</td>
                            <td class="text-end text-primary fs-5">{{ number_format($totalPartAssurance, 0, ',', ' ') }} FBU</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end">
            {{ $details->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layout')

@section('title', 'Relevé Détaillé Assurances (Tiers Payant) - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('rapports.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour au Centre de Rapports
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small font-mono">Tiers Payant</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="shield-check" class="lucide"></i>
                </div>
                <span>Relevé Détaillé des Prestations Prises en Charge</span>
            </h1>
            <p class="text-muted mb-0 small">
                Bordereau officiel d'encaissement et de recouvrement à destination des compagnies d'assurance maladie.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 no-print">
            <a href="{{ route('rapports.assurances.bordereau', request()->query()) }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="file-text" class="lucide-sm"></i>
                <span>Générer le Bordereau Officiel (A4)</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="printer" class="lucide-sm"></i>
                <span>Imprimer l'Écran</span>
            </button>
            <x-export-buttons table-id="rapportAssurancesTable" title="Releve_Tiers_Payant_Assurances" filename="releve_tiers_payant_assurances" />
        </div>
    </div>

    {{-- Filtres de sélection et recherche --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white no-print">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="filter" class="lucide-sm text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">Critères de Sélection du Bordereau</h6>
                </div>
                {{-- Raccourcis de dates rapides --}}
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <span class="small text-muted me-1">Période rapide :</span>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::today()->toDateString() }}" data-end="{{ \Carbon\Carbon::today()->toDateString() }}">Aujourd'hui</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfWeek()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfWeek()->toDateString() }}">Cette Semaine</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfMonth()->toDateString() }}">Ce Mois</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString() }}">Mois Dernier</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-period" data-start="{{ \Carbon\Carbon::now()->startOfYear()->toDateString() }}" data-end="{{ \Carbon\Carbon::now()->endOfYear()->toDateString() }}">Cette Année</button>
                </div>
            </div>

            <form method="GET" action="{{ route('rapports.assurances') }}" id="filterForm" class="row g-3 align-items-end">
                {{-- Sélection de l'organisme d'assurance --}}
                <div class="col-lg-4 col-md-6">
                    <label for="assurance_id" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="building-2" class="lucide-sm text-muted"></i>
                        <span>Organisme d'Assurance</span>
                    </label>
                    <select name="assurance_id" id="assurance_id" class="form-select fw-semibold">
                        <option value="">-- Toutes les Assurances Partenaires --</option>
                        @foreach($assurances as $assurance)
                            <option value="{{ $assurance->id }}" {{ ($assuranceId == $assurance->id || $assuranceId == $assurance->code) ? 'selected' : '' }}>
                                {{ $assurance->nom }} @if($assurance->code)({{ $assurance->code }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Début --}}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label for="date_debut" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="calendar" class="lucide-sm text-muted"></i>
                        <span>Date de Début</span>
                    </label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control font-mono" value="{{ $dateDebut }}" required>
                </div>

                {{-- Date Fin --}}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label for="date_fin" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="calendar" class="lucide-sm text-muted"></i>
                        <span>Date de Fin</span>
                    </label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control font-mono" value="{{ $dateFin }}" required>
                </div>

                {{-- Boutons d'action --}}
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                        <i data-lucide="search" class="lucide-sm"></i>
                        <span>Filtrer</span>
                    </button>
                    <a href="{{ route('rapports.assurances') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Réinitialiser les filtres">
                        <i data-lucide="rotate-ccw" class="lucide-sm"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Synthèse Financière (Cartes KPIs Modernes) --}}
    <div class="row g-3 mb-4">
        {{-- Total Brut Facturé --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 border-start border-4 border-secondary position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Facturé Brut (Public)</span>
                    <div class="bg-light text-secondary p-2 rounded-circle">
                        <i data-lucide="receipt" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-dark font-mono">
                    {{ number_format($totalTarifPublic, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <p class="text-muted small mb-0 mt-2">
                    Valeur nominale totale des actes médicaux
                </p>
            </div>
        </div>

        {{-- Part Payée par les Assurés (Patients) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 border-start border-4 border-success position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Part Payée par Assurés (Patients)</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i data-lucide="wallet-cards" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-success font-mono">
                    {{ number_format($totalPartPatient, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <p class="text-muted small mb-0 mt-2">
                    Ticket modérateur encaissé au guichet
                </p>
            </div>
        </div>

        {{-- Part Due par l'Assurance (Tiers Payant) --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 border-start border-4 border-primary position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Part Due par l'Assurance (Tiers Payant)</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="shield-alert" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-primary font-mono">
                    {{ number_format($totalPartAssurance, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <p class="text-muted small mb-0 mt-2">
                    Montant net officiel à recouvrer auprès des mutuelles
                </p>
            </div>
        </div>
    </div>

    {{-- En-tête exclusif impression --}}
    <div class="d-none d-print-block mb-4 pb-3 border-bottom">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="fw-bold text-dark mb-1">CLINIQUE GAHAMBANI — BAMAKO</h3>
                <p class="small text-muted mb-0">Plateau Médical & Services de Soins Spécialisés &bull; Tél: (+223) 00 00 00 00</p>
                <h4 class="mt-3 fw-bold text-primary">BORDEREAU MENSUEL DE PRISE EN CHARGE (TIERS PAYANT)</h4>
                <p class="mb-0">Organisme : <strong>{{ $assuranceSelected->nom ?? 'Toutes Sociétés d\'Assurance Confondues' }}</strong> &bull; Période du <strong>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</strong></p>
            </div>
            <div class="text-end">
                <span class="small text-muted">Édité le : {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span><br>
                <span class="small text-muted">Agent : {{ auth()->user()->prenom ?? '' }} {{ auth()->user()->nom ?? 'Administration' }}</span>
            </div>
        </div>
    </div>

    {{-- Tableau justificatif détaillé ligne par ligne conforme au cahier des charges --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="file-spreadsheet" class="lucide text-primary"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    Registre des Lignes Prises en Charge
                    @if($assuranceSelected)
                        <span class="text-muted fs-6 fw-normal">— {{ $assuranceSelected->nom }}</span>
                    @endif
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <span class="badge bg-light text-dark border font-mono small">
                    {{ $details->total() }} prestation(s) répertoriée(s)
                </span>
                <a href="{{ route('rapports.assurances.bordereau', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i data-lucide="file-check" style="width: 14px; height: 14px;"></i>
                    <span>Bordereau A4</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="rapportAssurancesTable" class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 130px;">N° Ticket / Réf</th>
                        <th style="width: 120px;">Date Visite</th>
                        <th>Patient Assuré</th>
                        <th>Organisme Assureur</th>
                        <th>Prestation / Acte</th>
                        <th class="text-end" style="width: 120px;">Tarif Public</th>
                        <th class="text-center" style="width: 90px;">Taux (%)</th>
                        <th class="text-end text-success" style="width: 130px;">Part Patient</th>
                        <th class="text-end text-primary pe-3" style="width: 140px;">Part Assurance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                        @php
                            $tarifLigne = (float) ($detail->montant_total ?? ($detail->prix_unitaire * $detail->quantite));
                            $tauxLigne = (float) ($detail->taux_couverture ?? 0);
                            $partPatientLigne = (float) ($detail->montant_patient ?? 0);
                            $partAssuranceLigne = (float) ($detail->montant_assurance ?? 0);
                            $patient = $detail->ticket->patient ?? null;
                            $assuranceTicket = $detail->ticket->assurance ?? null;
                        @endphp
                        <tr>
                            {{-- Référence Ticket --}}
                            <td class="ps-3">
                                @if($detail->ticket)
                                    <a href="{{ route('tickets.show', $detail->ticket) }}" class="badge bg-light text-primary border border-primary-subtle text-decoration-none font-mono py-1 px-2" title="Consulter le ticket">
                                        {{ $detail->ticket->reference }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary font-mono">#{{ $detail->ticket_id }}</span>
                                @endif
                            </td>

                            {{-- Date Visite --}}
                            <td>
                                <span class="small font-mono text-dark d-block">
                                    {{ $detail->ticket && $detail->ticket->date_ticket ? \Carbon\Carbon::parse($detail->ticket->date_ticket)->format('d/m/Y') : '-' }}
                                </span>
                                <span class="small text-muted font-mono" style="font-size: 0.75rem;">
                                    {{ $detail->ticket && $detail->ticket->date_ticket ? \Carbon\Carbon::parse($detail->ticket->date_ticket)->format('H:i') : '' }}
                                </span>
                            </td>

                            {{-- Patient Assuré --}}
                            <td>
                                @if($patient)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold small flex-shrink-0" style="width: 32px; height: 32px; font-size: 11px;">
                                            {{ strtoupper(substr($patient->prenom ?? 'P', 0, 1) . substr($patient->nom ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('patients.show', $patient) }}" class="fw-bold text-dark text-decoration-none d-block lh-sm">
                                                {{ $patient->prenom }} {{ $patient->nom }}
                                            </a>
                                            <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                                                @if($patient->sexe)
                                                    <span class="badge bg-light text-secondary border font-mono" style="font-size: 0.7rem;">{{ $patient->sexe === 'M' ? 'Homme' : 'Femme' }}</span>
                                                @endif
                                                @if($patient->matricule)
                                                    <span class="font-mono text-muted" style="font-size: 0.75rem;">Mat: {{ $patient->matricule }}</span>
                                                @elseif($patient->telephone)
                                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $patient->telephone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Patient Inconnu</span>
                                @endif
                            </td>

                            {{-- Assurance --}}
                            <td>
                                @if($assuranceTicket)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                        <i data-lucide="shield" style="width: 12px; height: 12px;"></i>
                                        <span>{{ $assuranceTicket->nom }}</span>
                                    </span>
                                    @if($assuranceTicket->code)
                                        <span class="d-block small text-muted font-mono mt-1" style="font-size: 0.75rem;">Code: {{ $assuranceTicket->code }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-muted border">Non renseigné</span>
                                @endif
                            </td>

                            {{-- Prestation / Acte --}}
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $detail->designation ?? $detail->prestation->acte->nom ?? $detail->prestation->service->nom ?? 'Prestation Médicale' }}
                                </div>
                                @if($detail->prestation && $detail->prestation->service)
                                    <span class="small text-muted" style="font-size: 0.75rem;">
                                        Service : {{ $detail->prestation->service->nom }}
                                    </span>
                                @endif
                            </td>

                            {{-- Tarif Brut Public --}}
                            <td class="text-end font-mono fw-semibold text-dark">
                                {{ number_format($tarifLigne, 0, ',', ' ') }} <small class="text-muted">F</small>
                            </td>

                            {{-- Taux de Prise en Charge --}}
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono fw-bold px-2 py-1">
                                    {{ (int) $tauxLigne }}%
                                </span>
                            </td>

                            {{-- Part Assuré --}}
                            <td class="text-end font-mono text-success fw-semibold">
                                {{ number_format($partPatientLigne, 0, ',', ' ') }} <small class="text-muted">F</small>
                            </td>

                            {{-- Part Assurance Due --}}
                            <td class="text-end font-mono text-primary fw-bold pe-3">
                                {{ number_format($partAssuranceLigne, 0, ',', ' ') }} <small class="text-primary">FCFA</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <i data-lucide="shield-alert" class="text-muted" style="width: 32px; height: 32px;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Aucune prise en charge d'assurance trouvée</h6>
                                    <p class="text-muted small mb-3">Aucun ticket avec tiers payant n'a été enregistré sur la période ou pour l'assurance sélectionnée.</p>
                                    <a href="{{ route('rapports.assurances') }}" class="btn btn-sm btn-outline-secondary">
                                        Réinitialiser la période
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($details->count() > 0)
                    <tfoot class="table-light fw-bold font-mono border-top border-2">
                        <tr>
                            <td colspan="5" class="ps-3 text-uppercase small text-dark py-3">
                                Totaux Généraux du Relevé :
                            </td>
                            <td class="text-end text-dark py-3">
                                {{ number_format($totalTarifPublic, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-center py-3">
                                <span class="badge bg-secondary font-mono">Total</span>
                            </td>
                            <td class="text-end text-success py-3">
                                {{ number_format($totalPartPatient, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end text-primary pe-3 py-3 fs-6">
                                {{ number_format($totalPartAssurance, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($details->hasPages())
            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center border-top no-print">
                <span class="small text-muted">Affichage de {{ $details->firstItem() }} à {{ $details->lastItem() }} sur {{ $details->total() }} lignes</span>
                {{ $details->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Bloc de signature et visa (réservé impression) --}}
    <div class="d-none d-print-block mt-5 pt-4 border-top">
        <div class="row text-center">
            <div class="col-4">
                <p class="fw-bold mb-5 small text-uppercase">Le Responsable Facturation</p>
                <p class="text-muted small">Date & Cachet</p>
            </div>
            <div class="col-4">
                <p class="fw-bold mb-5 small text-uppercase">Le Directeur Administratif</p>
                <p class="text-muted small">Signature & Cachet</p>
            </div>
            <div class="col-4">
                <p class="fw-bold mb-5 small text-uppercase">Réception Compagnie d'Assurance</p>
                <p class="text-muted small">Date d'accusé de réception</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boutons de raccourci de dates
    document.querySelectorAll('.quick-period').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('date_debut').value = this.dataset.start;
            document.getElementById('date_fin').value = this.dataset.end;
            document.getElementById('filterForm').submit();
        });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print, nav, aside, .sidebar, footer, .btn, .breadcrumb, header {
        display: none !important;
    }
    body {
        background: #fff !important;
        font-size: 11pt !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    th, td {
        padding: 6px 8px !important;
        font-size: 9.5pt !important;
    }
}
</style>
@endpush

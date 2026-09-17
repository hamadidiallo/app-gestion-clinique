@extends('layout')

@section('title', 'Dossier Médical : ' . $patient->prenom . ' ' . $patient->nom)

@section('content')
<div class="container-fluid p-0">
    {{-- BARRE SUPÉRIEURE AVEC BREADCRUMB & RETOUR --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('patients.index') }}" class="btn btn-light border bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour aux patients">
                <i data-lucide="arrow-left" style="width: 1.15rem; height: 1.15rem;"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $patient->prenom }} {{ $patient->nom }}</h1>
                    <span class="badge {{ $patient->sexe === 'M' ? 'badge-info-pill' : 'badge-danger-pill' }} px-2 py-1">
                        {{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}
                    </span>
                    @if($patient->statut === 'assure')
                        <span class="badge badge-success-pill px-2 py-1 d-flex align-items-center gap-1">
                            <i data-lucide="shield-check" style="width: 0.85rem; height: 0.85rem;"></i>
                            <span>Assuré ({{ $patient->taux_couverture ?? 80 }}%)</span>
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                            Non Assuré (Privé)
                        </span>
                    @endif
                </div>
                <small class="text-muted">
                    Dossier N° : <span class="font-mono fw-bold text-teal">{{ $patient->dossierMedical->numero_dossier ?? ('DM-' . date('Y') . '-' . str_pad((string)$patient->id, 5, '0', STR_PAD_LEFT)) }}</span>
                    &bull; Enregistré le <span class="font-mono">{{ $patient->created_at ? $patient->created_at->format('d/m/Y') : '-' }}</span>
                </small>
            </div>
        </div>

        {{-- BOUTONS D'ACTION RAPIDE --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Nouveau Ticket (Facturation)</span>
            </a>
            <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="activity" style="width: 1rem; height: 1rem;"></i>
                <span>+ Constantes / Consultation</span>
            </a>
            <button type="button" class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalDossierMedical">
                <i data-lucide="file-text" style="width: 1rem; height: 1rem;"></i>
                <span>Terrain & Antécédents</span>
            </button>
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Modifier fiche d'état civil">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
            </a>
        </div>
    </div>

    {{-- CARTE D'ALERTE MÉDICALE (ALLERGIES ET GROUPE SANGUIN) SI RENSEIGNÉ --}}
    @if($patient->dossierMedical && ($patient->dossierMedical->allergies || $patient->dossierMedical->groupe_sanguin))
        <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 rounded-3 mb-4 shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-danger text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i data-lucide="alert-triangle" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <div>
                        <strong class="text-danger-emphasis d-block">Alertes Médicales & Terrain Patient</strong>
                        <div class="d-flex flex-wrap gap-3 mt-1 small">
                            @if($patient->dossierMedical->groupe_sanguin)
                                <span>Groupe Sanguin : <strong class="badge bg-danger fs-7">{{ $patient->dossierMedical->groupe_sanguin }}</strong></span>
                            @endif
                            @if($patient->dossierMedical->allergies)
                                <span>Allergies signalées : <strong class="text-danger">{{ $patient->dossierMedical->allergies }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalDossierMedical">
                    <i data-lucide="edit-3" style="width: 0.85rem; height: 0.85rem;"></i>
                    <span>Modifier terrain</span>
                </button>
            </div>
        </div>
    @endif

    {{-- CARTES RÉCAPITULATIVES D'INDICATEURS DU PATIENT --}}
    @php
        $totalActes = $patient->tickets->flatMap->details->count();
        $totalFacture = $patient->tickets->sum('montant_total');
        $totalPayePatient = $patient->tickets->sum('montant_paye');
        $totalDetteRestante = $patient->dettes->where('statut', '!=', 'reglee')->sum('reste_a_payer');
    @endphp
    <div class="row g-3 mb-4">
        {{-- Actes & Lignes commandées --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100 bg-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Actes & Produits</span>
                        <h3 class="fw-bold mb-0 text-teal font-mono">{{ $totalActes }}</h3>
                        <small class="text-muted">{{ $patient->tickets->count() }} Tickets émis</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 46px; height: 46px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="clipboard-list" style="width: 1.35rem; height: 1.35rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Facturé --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100 bg-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Facturé</span>
                        <h4 class="fw-bold mb-0 text-dark font-mono">{{ number_format($totalFacture, 0, ',', ' ') }} <small class="fs-7 text-muted">FCFA</small></h4>
                        <small class="text-teal">Dont assurance & patient</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-light text-muted" style="width: 46px; height: 46px;">
                        <i data-lucide="receipt" style="width: 1.35rem; height: 1.35rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Encaissé au Guichet --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100 bg-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Encaissé</span>
                        <h4 class="fw-bold mb-0 text-success font-mono">{{ number_format($totalPayePatient, 0, ',', ' ') }} <small class="fs-7 text-muted">FCFA</small></h4>
                        <small class="text-muted">Règlements reçus</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 46px; height: 46px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="check-circle" style="width: 1.35rem; height: 1.35rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dette / Reste dû --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100 bg-white rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1">Reste Dû (Dette)</span>
                        <h4 class="fw-bold mb-0 {{ $totalDetteRestante > 0 ? 'text-danger' : 'text-muted' }} font-mono">
                            {{ number_format($totalDetteRestante, 0, ',', ' ') }} <small class="fs-7 text-muted">FCFA</small>
                        </h4>
                        <small class="{{ $totalDetteRestante > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $totalDetteRestante > 0 ? 'Impayés à recouvrer' : 'Compte soldé' }}
                        </small>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 46px; height: 46px; background: {{ $totalDetteRestante > 0 ? '#fce4e4' : '#f8f9fa' }}; color: {{ $totalDetteRestante > 0 ? '#b3261e' : '#6c757d' }};">
                        <i data-lucide="alert-octagon" style="width: 1.35rem; height: 1.35rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ONGLETS DU DOSSIER PATIENT --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-5 bg-white">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0" id="patientTabs" role="tablist">
                {{-- Onglet 1 : Actes & Soins demandés --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-4 fw-semibold d-flex align-items-center gap-2" id="actes-tab" data-bs-toggle="tab" data-bs-target="#tab-actes" type="button" role="tab">
                        <i data-lucide="list-checks" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Actes, Médicaments & Hospitalisations</span>
                        <span class="badge bg-teal rounded-pill font-mono">{{ $totalActes }}</span>
                    </button>
                </li>

                {{-- Onglet 2 : Constantes & Consultations Médicales --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold d-flex align-items-center gap-2" id="consultations-tab" data-bs-toggle="tab" data-bs-target="#tab-consultations" type="button" role="tab">
                        <i data-lucide="heart-pulse" style="width: 1.1rem; height: 1.1rem;" class="text-danger"></i>
                        <span>Constantes & Consultations</span>
                        <span class="badge bg-secondary rounded-pill font-mono">{{ $patient->consultations->count() }}</span>
                    </button>
                </li>

                {{-- Onglet 3 : Dossier Permanent & Antécédents --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold d-flex align-items-center gap-2" id="dossier-tab" data-bs-toggle="tab" data-bs-target="#tab-dossier" type="button" role="tab">
                        <i data-lucide="file-heart" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Terrain & Antécédents</span>
                    </button>
                </li>

                {{-- Onglet 4 : Tickets de Caisse & Factures --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold d-flex align-items-center gap-2" id="factures-tab" data-bs-toggle="tab" data-bs-target="#tab-factures" type="button" role="tab">
                        <i data-lucide="receipt" style="width: 1.1rem; height: 1.1rem;" class="text-warning"></i>
                        <span>Facturation & Tickets</span>
                        <span class="badge bg-secondary rounded-pill font-mono">{{ $patient->tickets->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="patientTabsContent">
                
                {{-- TAB 1 : ACTES, MÉDICAMENTS ET HOSPITALISATIONS --}}
                <div class="tab-pane fade show active" id="tab-actes" role="tabpanel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Historique des Actes & Soins Réalisés</h5>
                            <p class="text-muted small mb-0">Alimenté directement par les tickets de caisse émis pour ce patient.</p>
                        </div>
                        <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-teal fw-semibold d-flex align-items-center gap-1">
                            <i data-lucide="plus" style="width: 0.95rem; height: 0.95rem;"></i>
                            <span>Facturer un nouvel Acte / Médicament</span>
                        </a>
                    </div>

                    @php
                        $allDetails = $patient->tickets->sortByDesc('date_ticket')->flatMap(function ($ticket) {
                            return $ticket->details->map(function ($detail) use ($ticket) {
                                $detail->parent_ticket = $ticket;
                                return $detail;
                            });
                        });
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Désignation de la Prestation / Produit</th>
                                    <th>Service & Médecin</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Montant Total</th>
                                    <th class="text-center">Statut Règlement</th>
                                    <th class="text-end pe-3">Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allDetails as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block font-mono">
                                                {{ \Carbon\Carbon::parse($item->parent_ticket->date_ticket)->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted font-mono">{{ \Carbon\Carbon::parse($item->parent_ticket->date_ticket)->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if(($item->type_item ?? '') === 'medicament')
                                                <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                                    <i data-lucide="pill" style="width: 0.8rem; height: 0.8rem;"></i>
                                                    <span>Médicament</span>
                                                </span>
                                            @elseif(($item->type_item ?? '') === 'hospitalisation')
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
                                            <strong class="text-dark">{{ $item->libelle }}</strong>
                                            @if($item->prestation && $item->prestation->description)
                                                <small class="text-muted d-block">{{ $item->prestation->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="d-block text-dark fw-semibold">
                                                {{ $item->parent_ticket->service->nom ?? ($item->prestation->service->nom ?? 'Service Général') }}
                                            </small>
                                            <small class="text-muted">
                                                {{ $item->parent_ticket->medecin ? 'Dr ' . $item->parent_ticket->medecin->nom : ($item->prestation && $item->prestation->medecin ? 'Dr ' . $item->prestation->medecin->nom : 'Non assigné') }}
                                            </small>
                                        </td>
                                        <td class="text-center fw-bold font-mono">
                                            {{ number_format($item->quantite, 0) }}
                                        </td>
                                        <td class="text-end fw-bold text-dark font-mono">
                                            {{ number_format($item->montant_total, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-center">
                                            @if($item->parent_ticket->statut === 'paye')
                                                <span class="badge badge-success-pill">Payé</span>
                                            @elseif($item->parent_ticket->statut === 'partiellement_paye')
                                                <span class="badge badge-warning-pill">Partiel</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border">En attente</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('tickets.print', $item->parent_ticket) }}" class="btn btn-sm btn-outline-teal py-0 px-2 font-mono" title="Imprimer le reçu">
                                                <i data-lucide="printer" style="width: 0.85rem; height: 0.85rem;" class="me-1"></i>{{ $item->parent_ticket->reference }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i data-lucide="inbox" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-muted"></i>
                                            Aucun acte ou prestation n'a encore été facturé pour ce patient.<br>
                                            <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-teal mt-2">
                                                + Émettre le premier ticket
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2 : CONSTANTES & CONSULTATIONS MÉDICALES --}}
                <div class="tab-pane fade" id="tab-consultations" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Historique des Consultations & Constantes</h5>
                            <p class="text-muted small mb-0">Paramètres vitaux, observations cliniques et ordonnances délivrées.</p>
                        </div>
                        <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-teal fw-semibold d-flex align-items-center gap-1">
                            <i data-lucide="plus" style="width: 0.95rem; height: 0.95rem;"></i>
                            <span>Nouvelle Consultation</span>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Réf / Date</th>
                                    <th>Médecin</th>
                                    <th>Constantes Vitales</th>
                                    <th>Motif & Diagnostic</th>
                                    <th>Ordonnance</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->consultations as $cs)
                                    <tr>
                                        <td>
                                            <a href="{{ route('consultations.show', $cs) }}" class="fw-bold text-teal text-decoration-none font-mono">
                                                {{ $cs->reference }}
                                            </a>
                                            <small class="d-block text-muted font-mono">{{ $cs->date_consultation->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong class="text-dark">Dr {{ $cs->medecin ? $cs->medecin->nom : 'Généraliste' }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($cs->tension_arterielle)
                                                    <span class="badge bg-light text-dark border font-mono">TA: {{ $cs->tension_arterielle }}</span>
                                                @endif
                                                @if($cs->temperature)
                                                    <span class="badge bg-light text-dark border font-mono">{{ $cs->temperature }}°C</span>
                                                @endif
                                                @if($cs->poids)
                                                    <span class="badge bg-light text-dark border font-mono">{{ $cs->poids }} kg</span>
                                                @endif
                                                @if($cs->glycemie)
                                                    <span class="badge bg-light text-dark border font-mono">Glyc: {{ $cs->glycemie }} g/L</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-dark d-block">{{ \Illuminate\Support\Str::limit($cs->motif_consultation, 30) }}</strong>
                                            @if($cs->diagnostic)
                                                <small class="text-teal">{{ \Illuminate\Support\Str::limit($cs->diagnostic, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cs->ordonnance)
                                                <a href="{{ route('consultations.print-ordonnance', $cs) }}" target="_blank" class="btn btn-sm btn-outline-teal py-0 px-2 d-inline-flex align-items-center gap-1">
                                                    <i data-lucide="file-text" style="width: 0.85rem; height: 0.85rem;"></i>
                                                    <span>Ordonnance ({{ $cs->ordonnance->lignes->count() }})</span>
                                                </a>
                                            @else
                                                <span class="text-muted small">Aucune</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('consultations.show', $cs) }}" class="btn btn-sm btn-light border py-1 px-2 text-teal" title="Consulter la fiche">
                                                <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i data-lucide="stethoscope" style="width: 2.5rem; height: 2.5rem;" class="d-block mx-auto mb-2 text-muted"></i>
                                            Aucune consultation médicale enregistrée pour ce patient.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3 : DOSSIER PERMANENT & ANTÉCÉDENTS --}}
                <div class="tab-pane fade" id="tab-dossier" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Dossier Médical Permanent du Patient</h5>
                            <p class="text-muted small mb-0">Informations médicales pérennes (Groupe sanguin, allergies, antécédents familiaux et chirurgicaux).</p>
                        </div>
                        <button type="button" class="btn btn-teal fw-semibold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalDossierMedical">
                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                            <span>Modifier / Compléter ce Dossier</span>
                        </button>
                    </div>

                    <div class="row g-4">
                        {{-- Terrain & Groupe Sanguin --}}
                        <div class="col-md-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i data-lucide="droplet" style="width: 1.1rem; height: 1.1rem;" class="text-danger"></i>
                                    <span>Groupe Sanguin & Allergies</span>
                                </h6>
                                
                                <div class="mb-3">
                                    <span class="text-muted small d-block mb-1">Groupe Sanguin / Rhésus :</span>
                                    @if($patient->dossierMedical && $patient->dossierMedical->groupe_sanguin)
                                        <span class="badge bg-danger fs-5 px-3 py-2 font-mono">{{ $patient->dossierMedical->groupe_sanguin }}</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Non déterminé</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-muted small d-block mb-1">Allergies Connues :</span>
                                    @if($patient->dossierMedical && $patient->dossierMedical->allergies)
                                        <div class="p-2 rounded bg-danger-subtle text-danger fw-bold">
                                            {{ $patient->dossierMedical->allergies }}
                                        </div>
                                    @else
                                        <span class="text-muted">Aucune allergie signalée</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Antécédents Personnels et Chirurgicaux --}}
                        <div class="col-md-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i data-lucide="user" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                                    <span>Antécédents Personnels</span>
                                </h6>
                                
                                <div class="mb-3">
                                    <span class="text-muted small d-block mb-1">Antécédents Médicaux :</span>
                                    <p class="mb-0 text-dark fw-semibold">
                                        {{ $patient->dossierMedical->antecedents_personnels ?? 'Néant' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-muted small d-block mb-1">Antécédents Chirurgicaux :</span>
                                    <p class="mb-0 text-dark fw-semibold">
                                        {{ $patient->dossierMedical->antecedents_chirurgicaux ?? 'Néant' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Antécédents Familiaux & Notes --}}
                        <div class="col-md-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i data-lucide="users" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                                    <span>Antécédents Familiaux</span>
                                </h6>
                                
                                <div class="mb-3">
                                    <span class="text-muted small d-block mb-1">Antécédents Familiaux :</span>
                                    <p class="mb-0 text-dark fw-semibold">
                                        {{ $patient->dossierMedical->antecedents_familiaux ?? 'Néant' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-muted small d-block mb-1">Notes Particulières :</span>
                                    <p class="mb-0 text-muted small">
                                        {{ $patient->dossierMedical->notes_particulieres ?? 'Aucune note spécifique' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 4 : TICKETS DE FACTURATION & PAIEMENTS --}}
                <div class="tab-pane fade" id="tab-factures" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Historique des Tickets & Factures de Caisse</h5>
                            <p class="text-muted small mb-0">Détail des montants facturés, paiements enregistrés et créances.</p>
                        </div>
                        <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-teal fw-semibold d-flex align-items-center gap-1">
                            <i data-lucide="plus" style="width: 0.95rem; height: 0.95rem;"></i>
                            <span>Nouveau Ticket</span>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th class="text-end">Total Brut</th>
                                    <th class="text-end">Part Assurance</th>
                                    <th class="text-end">Net Patient</th>
                                    <th class="text-end">Payé</th>
                                    <th class="text-end">Reste Dû</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->tickets as $tck)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tickets.show', $tck) }}" class="fw-bold text-teal font-mono text-decoration-none">
                                                {{ $tck->reference }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="small text-muted font-mono">{{ \Carbon\Carbon::parse($tck->date_ticket)->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark font-mono">
                                            {{ number_format($tck->montant_total, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end text-teal fw-semibold font-mono">
                                            {{ number_format($tck->montant_assurance, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end fw-bold text-dark font-mono">
                                            {{ number_format($tck->montant_patient, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end text-success fw-bold font-mono">
                                            {{ number_format($tck->montant_paye, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end fw-bold {{ $tck->reste_a_payer > 0 ? 'text-danger' : 'text-muted' }} font-mono">
                                            {{ number_format($tck->reste_a_payer, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-center">
                                            @if($tck->statut === 'paye')
                                                <span class="badge badge-success-pill">Payé</span>
                                            @elseif($tck->statut === 'partiellement_paye')
                                                <span class="badge badge-warning-pill">Partiel</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border">En attente</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('tickets.print', $tck) }}" class="btn btn-sm btn-light border text-teal py-1 px-2" title="Imprimer Reçu">
                                                <i data-lucide="printer" style="width: 0.95rem; height: 0.95rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            Aucun ticket de facturation émis pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL : MISE À JOUR DU DOSSIER MÉDICAL PERMANENT --}}
<div class="modal fade" id="modalDossierMedical" tabindex="-1" aria-labelledby="modalDossierMedicalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('patients.dossier-medical.update', $patient) }}" method="POST" class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            @csrf
            <div class="modal-header bg-teal text-white py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalDossierMedicalLabel">
                    <i data-lucide="file-heart" style="width: 1.25rem; height: 1.25rem;"></i>
                    <span>Dossier Médical Permanent : {{ $patient->prenom }} {{ $patient->nom }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="groupe_sanguin" class="form-label fw-bold text-dark small">Groupe Sanguin</label>
                        <select name="groupe_sanguin" id="groupe_sanguin" class="form-select fw-bold font-mono">
                            <option value="">-- Non déterminé --</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gs)
                                <option value="{{ $gs }}" {{ (old('groupe_sanguin', $patient->dossierMedical->groupe_sanguin ?? '') === $gs) ? 'selected' : '' }}>
                                    {{ $gs }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="allergies" class="form-label fw-bold text-danger small">Allergies (Médicamenteuses, alimentaires...)</label>
                        <input type="text" name="allergies" id="allergies" class="form-control" value="{{ old('allergies', $patient->dossierMedical->allergies ?? '') }}" placeholder="Ex: Pénicilline, Aspirine, Bétadine...">
                    </div>

                    <div class="col-md-12">
                        <label for="antecedents_personnels" class="form-label fw-bold text-dark small">Antécédents Médicaux Personnels</label>
                        <textarea name="antecedents_personnels" id="antecedents_personnels" class="form-control" rows="2" placeholder="Ex: HTA depuis 2018, Diabète Type 2, Drépanocytose...">{{ old('antecedents_personnels', $patient->dossierMedical->antecedents_personnels ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="antecedents_chirurgicaux" class="form-label fw-bold text-dark small">Antécédents Chirurgicaux</label>
                        <textarea name="antecedents_chirurgicaux" id="antecedents_chirurgicaux" class="form-control" rows="2" placeholder="Ex: Appendicectomie (2015), Césarienne (2020)...">{{ old('antecedents_chirurgicaux', $patient->dossierMedical->antecedents_chirurgicaux ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="antecedents_familiaux" class="form-label fw-bold text-dark small">Antécédents Familiaux</label>
                        <textarea name="antecedents_familiaux" id="antecedents_familiaux" class="form-control" rows="2" placeholder="Ex: Père diabétique, mère hypertendue...">{{ old('antecedents_familiaux', $patient->dossierMedical->antecedents_familiaux ?? '') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="notes_particulieres" class="form-label fw-bold text-dark small">Notes Particulières / Recommandations</label>
                        <textarea name="notes_particulieres" id="notes_particulieres" class="form-control" rows="2" placeholder="Consignes particulières...">{{ old('notes_particulieres', $patient->dossierMedical->notes_particulieres ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-teal fw-semibold px-4 d-flex align-items-center gap-1">
                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                    <span>Enregistrer le Dossier</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

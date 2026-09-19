@extends('layout')

@section('title', 'Émettre un Ticket de Facturation - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec navigation retour --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Caisse & Facturation</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Nouveau Ticket</span>
            </div>
            <h1 class="h3 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <i data-lucide="receipt" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <span>Émettre un Ticket de Facturation</span>
            </h1>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i data-lucide="arrow-left" class="lucide-sm"></i>
            <span>Retour à la Liste</span>
        </a>
    </div>

    <form action="{{ route('tickets.store') }}" method="POST" id="ticket_form" class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        @csrf

        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e;">
                    <i data-lucide="clipboard-list" class="lucide-sm"></i>
                </span>
                Informations Générales du Ticket
            </h5>
            <span class="badge bg-light text-muted border px-3 py-2 font-mono fw-bold">
                Réf: <span class="text-dark">{{ old('reference', $defaultReference) }}</span>
            </span>
        </div>

        <div class="card-body p-4">
            {{-- BLOC 1 : PATIENT & ASSURANCE --}}
            <div class="row g-3 mb-4">
                <input type="hidden" name="reference" id="reference" value="{{ old('reference', $defaultReference) }}">
                <input type="hidden" name="user_id" value="{{ $currentUser->id ?? 1 }}">

                {{-- Recherche Patient avec Autocomplétion --}}
                @php
                    $initPatientName = isset($selectedPatient) ? $selectedPatient->prenom . ' ' . $selectedPatient->nom : '';
                    $initPatientId = isset($selectedPatient) ? $selectedPatient->id : request()->get('patient_id');
                    $initCarte = isset($selectedPatient) && $selectedPatient->cartesAssurance ? $selectedPatient->cartesAssurance->where('statut', true)->first() : null;
                    $initAssuranceId = $selectedPatient->assurance_id ?? ($initCarte ? $initCarte->assurance_id : '');
                    $initTaux = $selectedPatient->taux_couverture ?? ($initCarte ? (float)$initCarte->taux_couverture : 0);

                    $initActeDesignation = isset($selectedActe) ? $selectedActe->nom : '';
                    $initActePrix = isset($selectedActe) ? (int)$selectedActe->tarif_normal : 0;
                    $initServiceId = isset($selectedActe) ? $selectedActe->service_id : old('service_id');
                @endphp
                <div class="col-lg-5 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="patient_search_input" class="form-label fw-bold text-dark d-flex align-items-center gap-1 mb-0">
                            <i data-lucide="user" class="lucide-sm text-primary"></i> Patient <span class="text-danger">*</span>
                        </label>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none text-teal fw-semibold p-0 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#quickPatientModal" id="btn_quick_patient_link" title="Créer un nouveau patient rapidement">
                            <i data-lucide="user-plus" style="width: 14px; height: 14px;"></i>
                            <span>+ Nouveau Patient</span>
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i data-lucide="search" class="lucide-sm"></i>
                        </span>
                        <input type="text" id="patient_search_input" class="form-control border-start-0 ps-1 @error('patient_id') is-invalid @enderror" placeholder="Tapez le nom, prénom ou téléphone..." data-url="{{ route('patients.search') }}" value="{{ old('patient_search_name', $initPatientName) }}" autocomplete="off" required>
                        <button type="button" class="btn btn-teal fw-bold d-inline-flex align-items-center gap-1 shadow-sm px-3" style="background-color: #0f766e !important; border-color: #0f766e !important; color: #ffffff !important;" data-bs-toggle="modal" data-bs-target="#quickPatientModal" id="btn_open_quick_patient" title="Créer un nouveau patient sans quitter la caisse">
                            <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
                            <span>Nouveau</span>
                        </button>
                    </div>
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $initPatientId) }}" required>
                    
                    {{-- Message de confirmation après création rapide --}}
                    <div id="quick_patient_success_badge" class="alert alert-success d-none py-1 px-2 small mt-1 mb-0 d-flex align-items-center gap-1 rounded-2">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                        <span id="quick_patient_success_text"></span>
                    </div>

                    {{-- Liste d'autocomplétion dynamique --}}
                    <div id="patient_results_list" class="list-group position-absolute w-100 shadow-lg rounded-3 mt-1" style="display: none; z-index: 1050; max-height: 280px; overflow-y: auto;"></div>
                    
                    @error('patient_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Organisme d'Assurance --}}
                <div class="col-lg-4">
                    <label for="assurance_id" class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="shield-check" class="lucide-sm text-success"></i> Prise en charge Assurance
                    </label>
                    <select name="assurance_id" id="assurance_id" class="form-select fw-semibold @error('assurance_id') is-invalid @enderror">
                        <option value="" data-taux="0">-- Aucune (100% Patient / Privé) --</option>
                        @foreach($assurances as $assurance)
                            <option value="{{ $assurance->id }}" data-taux="{{ $assurance->taux_par_defaut ?? 80 }}" {{ old('assurance_id', $initAssuranceId) == $assurance->id ? 'selected' : '' }}>
                                {{ $assurance->nom }} {{ $assurance->code ? '('.$assurance->code.')' : '' }} (Défaut: {{ number_format($assurance->taux_par_defaut ?? 80, 0) }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('assurance_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    
                    @php
                        $initNumAssure = $selectedPatient->numero_assure ?? ($initCarte ? $initCarte->reference : '');
                    @endphp
                    <div id="ticket_patient_assure_badge" class="small mt-1 text-teal fw-semibold {{ $initNumAssure ? '' : 'd-none' }}">
                        <i data-lucide="credit-card" style="width: 13px; height: 13px;"></i>
                        <span>N° Assuré : <strong id="ticket_patient_assure_num" class="font-mono text-dark">{{ $initNumAssure }}</strong></span>
                    </div>
                </div>

                {{-- Taux de Prise en charge en % --}}
                <div class="col-lg-3">
                    <label for="taux_assurance" class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="percent" class="lucide-sm text-success"></i> Taux Assurance
                    </label>
                    <div class="input-group">
                        <input type="number" step="1" min="0" max="100" name="taux_assurance" id="taux_assurance" class="form-control font-mono fw-bold text-success @error('taux_assurance') is-invalid @enderror" value="{{ old('taux_assurance', $initTaux) }}" placeholder="0">
                        <span class="input-group-text bg-success-subtle text-success fw-bold font-mono">%</span>
                    </div>
                </div>

                {{-- Service Médical --}}
                <div class="col-md-4">
                    <label for="service_id" class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="building-2" class="lucide-sm text-primary"></i> Service d'Orientation
                    </label>
                    <select name="service_id" id="service_id" class="form-select fw-semibold @error('service_id') is-invalid @enderror">
                        <option value="">-- Sélectionner un Service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id', $initServiceId) == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Médecin Traitant avec Recherche Rapide --}}
                <div class="col-md-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="medecin_search_input" class="form-label fw-bold text-dark d-flex align-items-center gap-1 mb-0">
                            <i data-lucide="user-check" class="lucide-sm text-primary"></i> Médecin Référent
                        </label>
                        @php
                            $initMedecinId = old('medecin_id', $selectedMedecin->id ?? '');
                            $initMedecinText = '';
                            if ($initMedecinId && $medecins->find($initMedecinId)) {
                                $mFound = $medecins->find($initMedecinId);
                                $initMedecinText = 'Dr ' . $mFound->nom . ' ' . $mFound->prenom . ($mFound->specialite ? ' (' . $mFound->specialite . ')' : '');
                            }
                        @endphp
                        <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0 small" id="btn_clear_medecin" style="{{ $initMedecinId ? '' : 'display: none;' }}" title="Désassigner le médecin">
                            ✕ Réinitialiser
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i data-lucide="search" class="lucide-sm"></i>
                        </span>
                        <input type="text" id="medecin_search_input" class="form-control border-start-0 ps-1" placeholder="Tapez le nom ou la spécialité..." value="{{ $initMedecinText }}" autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary" id="btn_toggle_medecins_dropdown" title="Afficher tous les médecins">
                            <i data-lucide="chevron-down" class="lucide-sm"></i>
                        </button>
                    </div>
                    <input type="hidden" name="medecin_id" id="medecin_id" value="{{ $initMedecinId }}">
                    
                    {{-- Liste dynamique des médecins filtrée --}}
                    <div id="medecin_results_list" class="list-group position-absolute w-100 shadow-lg rounded-3 mt-1" style="display: none; z-index: 1050; max-height: 260px; overflow-y: auto;"></div>
                </div>

                {{-- Dates --}}
                <div class="col-md-4">
                    <label for="date_ticket" class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i data-lucide="calendar" class="lucide-sm text-primary"></i> Date d'Émission
                    </label>
                    <input type="datetime-local" name="date_ticket" id="date_ticket" class="form-control font-mono" value="{{ old('date_ticket', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required>
                </div>
            </div>

            {{-- BLOC 2 : TABLEAU DYNAMIQUE DES ACTES, MÉDICAMENTS & FRAIS --}}
            <div class="card border rounded-3 mb-4 overflow-hidden">
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small d-flex align-items-center gap-1">
                        <i data-lucide="list" class="lucide-sm text-primary"></i> Lignes Facturables (Actes, Médicaments, Hospitalisation)
                    </span>
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" id="btn_add_row">
                        <i data-lucide="plus" class="lucide-sm"></i> Ajouter une ligne
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="items_table">
                        <thead>
                            <tr>
                                <th style="width: 22%;">Type de Prestation</th>
                                <th style="width: 38%;">Désignation / Libellé</th>
                                <th style="width: 12%;" class="text-center">Quantité</th>
                                <th style="width: 14%;" class="text-end">Prix Unitaire</th>
                                <th style="width: 14%;" class="text-end">Total Ligne</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items_table_body">
                            {{-- Ligne 1 par défaut : Consultation --}}
                            <tr class="item-row" data-index="0">
                                <td>
                                    <select name="items[0][type_item]" class="form-select form-select-sm item-type">
                                        <option value="acte" selected>🩺 Acte Médical</option>
                                        <option value="medicament">💊 Médicament</option>
                                        <option value="hospitalisation">🛏️ Hospitalisation</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[0][designation]" list="actes_datalist" class="form-control form-control-sm item-designation fw-semibold" value="{{ $initActeDesignation }}" placeholder="Rechercher un acte dans le catalogue..." required autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" step="1" min="1" name="items[0][quantite]" class="form-control form-control-sm text-center item-quantite font-mono fw-bold" value="1" required>
                                </td>
                                <td>
                                    <input type="number" step="100" min="0" name="items[0][prix_unitaire]" class="form-control form-control-sm text-end item-pu font-mono fw-bold" value="{{ $initActePrix }}" placeholder="0" required>
                                </td>
                                <td class="text-end font-mono fw-bold text-dark item-total-display">
                                    {{ number_format($initActePrix, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Supprimer">
                                        <i data-lucide="trash-2" class="lucide-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Datalist pour autocomplétion intelligente des actes du catalogue --}}
                <datalist id="actes_datalist">
                    @foreach($actes as $a)
                        <option value="{{ $a->nom }}" data-prix="{{ (int)$a->tarif_normal }}" data-prix-amo="{{ (int)($a->tarif_amo ?? $a->tarif_normal) }}" data-code="{{ $a->code }}" data-service-id="{{ $a->service_id }}">
                            [{{ $a->code }}] {{ $a->nom }} ({{ number_format($a->tarif_normal, 0, ',', ' ') }} FCFA)
                        </option>
                    @endforeach
                </datalist>

                {{-- Raccourcis d'ajout rapide --}}
                <div class="card-footer bg-light p-3 d-flex flex-wrap align-items-center gap-2 border-top">
                    <span class="text-muted small fw-bold me-1">Ajout rapide :</span>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="acte" data-name="Consultation Spécialiste" data-price="10000">Consultation Spécialiste</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="acte" data-name="Pansement / Soin infirmier" data-price="2500">Pansement / Soin</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="acte" data-name="Échographie Abdominale" data-price="15000">Échographie</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="medicament" data-name="Paracétamol 1g (Boîte)" data-price="1000">Paracétamol 1g</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="medicament" data-name="Amoxicilline 1g" data-price="2500">Amoxicilline 1g</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-add" data-type="hospitalisation" data-name="Chambre Hospitalisation (Journée)" data-price="15000">Hospitalisation / Jour</button>
                </div>
            </div>

            {{-- BLOC 3 : RÉCAPITULATIF FINANCIER & ENCAISSEMENT --}}
            <div class="row g-4">
                {{-- Totaux et Ventilation --}}
                <div class="col-lg-7">
                    <div class="card bg-light border-0 p-3 h-100 rounded-4">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i data-lucide="calculator" class="lucide-sm text-primary"></i>
                            <span>Décompte Financier</span>
                        </h6>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted">Total Brut des Prestations :</span>
                            <span class="font-mono fs-5 mb-0 fw-bold text-dark" id="display_total_brut">{{ number_format($initActePrix, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom text-success">
                            <span>Part Prise en Charge Assurance (<span id="display_taux_assurance" class="font-mono">0</span>%) :</span>
                            <span class="font-mono fw-bold" id="display_part_assurance">0 FCFA</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pt-1">
                            <span class="h6 mb-0 fw-bold text-dark">Net à Payer par le Patient :</span>
                            <span class="font-mono fs-4 mb-0 fw-bold" style="color: var(--primary-color);" id="display_net_patient">{{ number_format($initActePrix, 0, ',', ' ') }} FCFA</span>
                        </div>

                        {{-- Champs cachés transmis au serveur --}}
                        <input type="hidden" name="montant_total" id="montant_total" value="{{ $initActePrix }}">
                        <input type="hidden" name="montant_assurance" id="montant_assurance" value="0">
                        <input type="hidden" name="montant_patient" id="montant_patient" value="{{ $initActePrix }}">

                        <div class="mt-auto">
                            <label for="description" class="form-label small fw-bold text-muted mb-1">Motif / Observations complémentaires</label>
                            <textarea name="description" id="description" class="form-control form-control-sm" rows="2" placeholder="Ex: Consultation d'urgence, motif d'hospitalisation...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Encaissement Guichet & Caisse --}}
                <div class="col-lg-5">
                    <div class="card p-3 h-100 rounded-4 bg-white border">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i data-lucide="coins" class="lucide-sm text-primary"></i>
                            <span>Règlement au Guichet (Caisse)</span>
                        </h6>

                        <div class="mb-3">
                            <label for="montant_paye" class="form-label fw-bold text-dark mb-1">
                                Montant Encaissé (FCFA)
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="100" min="0" name="montant_paye" id="montant_paye" class="form-control font-mono fw-bold text-success" value="{{ old('montant_paye', 0) }}" placeholder="0">
                                <button type="button" class="btn btn-outline-success fw-bold" id="btn_payer_tout" title="Régler la totalité">
                                    Total
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="mode_paiement_id" class="form-label fw-bold text-dark mb-1">
                                Mode d'Encaissement
                            </label>
                            <select name="mode_paiement_id" id="mode_paiement_id" class="form-select fw-semibold">
                                @foreach($modesPaiement as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rendu monnaie ou Reste à payer --}}
                        <div class="p-3 rounded-3 bg-light border" id="box_recap_paiement">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Statut Ticket :</span>
                                <span class="badge bg-secondary fw-bold" id="badge_statut_ticket">En attente</span>
                                <input type="hidden" name="statut" id="statut_input" value="en_attente">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1" id="row_reste_payer">
                                <span class="small text-danger fw-semibold">Reste à payer (Dette) :</span>
                                <strong class="font-mono text-danger" id="display_reste_payer">{{ number_format($initActePrix, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-success d-none" id="row_monnaie_rendue">
                                <span class="small fw-semibold">Monnaie à rendre :</span>
                                <strong class="font-mono" id="display_monnaie_rendue">0 FCFA</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center border-top">
            <span class="text-muted small d-flex align-items-center gap-1">
                <i data-lucide="info" class="lucide-sm text-muted"></i>
                <span>Ticket valable 7 jours. Les actes seront immédiatement archivés dans le dossier médical.</span>
            </span>
            <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 d-inline-flex align-items-center gap-2">
                <i data-lucide="printer" class="lucide"></i>
                <span>Valider & Imprimer Reçu (80mm)</span>
            </button>
        </div>
    </form>

    <!-- Modale Création Rapide de Patient (Guichet / Caissière) -->
    <div class="modal fade" id="quickPatientModal" tabindex="-1" aria-labelledby="quickPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i data-lucide="user-plus" class="text-white" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="quickPatientModalLabel">Création Rapide de Patient</h5>
                            <small class="text-white text-opacity-75">Enregistrement express au guichet pour facturation immédiate</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form id="quick_patient_form" action="{{ route('patients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="quick_patient_alert" class="alert alert-danger d-none py-2 px-3 small rounded-3 mb-3"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="qp_prenom" class="form-label small fw-bold text-dark mb-1">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="prenom" id="qp_prenom" class="form-control rounded-3" placeholder="Ex: Awa, Mamadou..." required autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="qp_nom" class="form-label small fw-bold text-dark mb-1">Nom de famille <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="qp_nom" class="form-control rounded-3" placeholder="Ex: Traoré, Keita..." required autocomplete="off">
                            </div>

                            <div class="col-md-4">
                                <label for="qp_sexe" class="form-label small fw-bold text-dark mb-1">Sexe <span class="text-danger">*</span></label>
                                <select name="sexe" id="qp_sexe" class="form-select rounded-3" required>
                                    <option value="M">Masculin (Homme)</option>
                                    <option value="F">Féminin (Femme)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="qp_telephone" class="form-label small fw-bold text-dark mb-1">Téléphone</label>
                                <input type="tel" name="telephone" id="qp_telephone" class="form-control rounded-3" placeholder="Ex: 70 12 34 56">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">Prise en charge <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group" aria-label="Type de prise en charge">
                                    <input type="radio" class="btn-check" name="statut" id="qp_statut_non_assure" value="non_assure" checked autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center justify-content-center gap-1 py-2" for="qp_statut_non_assure">
                                        <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                                        <span>Privé (Non Assuré)</span>
                                    </label>

                                    <input type="radio" class="btn-check" name="statut" id="qp_statut_assure" value="assure" autocomplete="off">
                                    <label class="btn btn-outline-teal btn-sm fw-semibold d-flex align-items-center justify-content-center gap-1 py-2" for="qp_statut_assure">
                                        <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i>
                                        <span>Assuré (Mutuelle)</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Champs spécifiques assurance (affichés seulement si statut == 'assure') --}}
                            <div class="col-12 d-none" id="qp_assurance_fields">
                                <div class="p-3 rounded-3 bg-light border border-teal" style="background-color: #f0fdfa !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-teal mb-0 d-flex align-items-center gap-2">
                                            <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                                            <span>Informations de l'Assurance du Patient</span>
                                        </h6>
                                        <span class="badge badge-info-pill">Tiers-payant</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label for="qp_assurance_id" class="form-label small fw-bold text-dark mb-1">
                                                Organisme d'Assurance <span class="text-danger">*</span>
                                            </label>
                                            <select name="assurance_id" id="qp_assurance_id" class="form-select rounded-2">
                                                <option value="" data-taux="0">-- Sélectionner une assurance --</option>
                                                @foreach($assurances as $assurance)
                                                    <option value="{{ $assurance->id }}" data-taux="{{ $assurance->taux_par_defaut ?? 80 }}">
                                                        {{ $assurance->nom }} {{ $assurance->code ? '('.$assurance->code.')' : '' }} (Défaut: {{ number_format($assurance->taux_par_defaut ?? 80, 0) }}%)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="qp_numero_assure" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                                                <i data-lucide="credit-card" style="width: 14px; height: 14px;" class="text-teal"></i>
                                                <span>Numéro d'Assuré / Matricule <span class="text-danger">*</span></span>
                                            </label>
                                            <input type="text" name="numero_assure" id="qp_numero_assure" class="form-control rounded-2 font-mono fw-semibold" placeholder="Ex: INPS-998811 ou CANAM-7788">
                                            <small class="text-muted" style="font-size: 0.72rem;">N° de carte ou matricule de l'assuré</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="qp_taux_couverture" class="form-label small fw-bold text-dark mb-1">
                                                Taux Prise en charge (%)
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="1" min="0" max="100" name="taux_couverture" id="qp_taux_couverture" class="form-control rounded-start-2 font-mono fw-bold text-success" placeholder="80">
                                                <span class="input-group-text bg-success-subtle text-success fw-bold font-mono">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="btn_submit_quick_patient" class="btn btn-primary rounded-pill px-4 fw-semibold d-inline-flex align-items-center gap-2" style="background: #0f766e; border-color: #0f766e;">
                            <span id="qp_spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <i data-lucide="check" class="lucide-sm" id="qp_btn_icon"></i>
                            <span>Enregistrer & Sélectionner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const tableBody = document.getElementById('items_table_body');
    const inputTauxAssurance = document.getElementById('taux_assurance');
    const selectAssurance = document.getElementById('assurance_id');
    const inputMontantPaye = document.getElementById('montant_paye');
    const btnPayerTout = document.getElementById('btn_payer_tout');

    // Éléments d'affichage
    const displayTotalBrut = document.getElementById('display_total_brut');
    const displayTauxAssurance = document.getElementById('display_taux_assurance');
    const displayPartAssurance = document.getElementById('display_part_assurance');
    const displayNetPatient = document.getElementById('display_net_patient');
    const displayRestePayer = document.getElementById('display_reste_payer');
    const displayMonnaieRendue = document.getElementById('display_monnaie_rendue');
    const rowMonnaieRendue = document.getElementById('row_monnaie_rendue');
    const badgeStatutTicket = document.getElementById('badge_statut_ticket');

    // Champs cachés
    const hiddenTotal = document.getElementById('montant_total');
    const hiddenAssurance = document.getElementById('montant_assurance');
    const hiddenPatient = document.getElementById('montant_patient');
    const hiddenStatut = document.getElementById('statut_input');

    function formatFCFA(val) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(val)) + ' FCFA';
    }

    function recalculerTotaux() {
        let totalBrut = 0;
        const rows = tableBody.querySelectorAll('.item-row');

        rows.forEach(row => {
            const qte = parseFloat(row.querySelector('.item-quantite').value) || 0;
            const pu = parseFloat(row.querySelector('.item-pu').value) || 0;
            const rowTotal = qte * pu;
            totalBrut += rowTotal;
            row.querySelector('.item-total-display').textContent = formatFCFA(rowTotal);
        });

        const taux = Math.max(0, Math.min(100, parseFloat(inputTauxAssurance.value) || 0));
        const partAssurance = Math.round((totalBrut * taux) / 100);
        const netPatient = Math.max(0, totalBrut - partAssurance);

        displayTotalBrut.textContent = formatFCFA(totalBrut);
        displayTauxAssurance.textContent = taux;
        displayPartAssurance.textContent = formatFCFA(partAssurance);
        displayNetPatient.textContent = formatFCFA(netPatient);

        hiddenTotal.value = totalBrut;
        hiddenAssurance.value = partAssurance;
        hiddenPatient.value = netPatient;

        // Calcul Paiement / Reste / Monnaie
        const recu = parseFloat(inputMontantPaye.value) || 0;
        const reste = Math.max(0, netPatient - recu);
        const monnaie = Math.max(0, recu - netPatient);

        displayRestePayer.textContent = formatFCFA(reste);

        if (monnaie > 0) {
            rowMonnaieRendue.classList.remove('d-none');
            displayMonnaieRendue.textContent = formatFCFA(monnaie);
        } else {
            rowMonnaieRendue.classList.add('d-none');
        }

        if (recu >= netPatient && netPatient > 0) {
            badgeStatutTicket.className = 'badge bg-success fw-bold';
            badgeStatutTicket.textContent = 'Payé';
            hiddenStatut.value = 'paye';
        } else if (recu > 0) {
            badgeStatutTicket.className = 'badge bg-warning text-dark fw-bold';
            badgeStatutTicket.textContent = 'Partiellement payé';
            hiddenStatut.value = 'partiellement_paye';
        } else {
            badgeStatutTicket.className = 'badge bg-secondary fw-bold';
            badgeStatutTicket.textContent = 'En attente';
            hiddenStatut.value = 'en_attente';
        }
    }

    function ajouterLigne(type, designation = '', prix = 0, qte = 1) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = rowIndex;

        let iconLabel = '🩺 Acte Médical';
        if (type === 'medicament') iconLabel = '💊 Médicament';
        if (type === 'hospitalisation') iconLabel = '🛏️ Hospitalisation';

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][type_item]" class="form-select form-select-sm item-type">
                    <option value="acte" ${type === 'acte' ? 'selected' : ''}>🩺 Acte Médical</option>
                    <option value="medicament" ${type === 'medicament' ? 'selected' : ''}>💊 Médicament</option>
                    <option value="hospitalisation" ${type === 'hospitalisation' ? 'selected' : ''}>🛏️ Hospitalisation</option>
                </select>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][designation]" list="actes_datalist" class="form-control form-control-sm item-designation fw-semibold" value="${designation}" placeholder="Nom de l'acte ou produit..." required autocomplete="off">
            </td>
            <td>
                <input type="number" step="1" min="1" name="items[${rowIndex}][quantite]" class="form-control form-control-sm text-center item-quantite fw-bold" value="${qte}" required>
            </td>
            <td>
                <input type="number" step="100" min="0" name="items[${rowIndex}][prix_unitaire]" class="form-control form-control-sm text-end item-pu fw-bold" value="${prix}" placeholder="0" required>
            </td>
            <td class="text-end fw-bold text-primary item-total-display">
                ${formatFCFA(prix * qte)}
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="Supprimer">
                    <i data-lucide="trash-2" class="lucide-sm"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(tr);
        rowIndex++;
        attachRowEvents(tr);
        recalculerTotaux();
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function attachRowEvents(row) {
        const inputs = row.querySelectorAll('.item-quantite, .item-pu');
        inputs.forEach(input => {
            input.addEventListener('input', recalculerTotaux);
        });

        const inputDesignation = row.querySelector('.item-designation');
        const inputPu = row.querySelector('.item-pu');

        if (inputDesignation && inputPu) {
            function updateFromDatalist(input) {
                const val = input.value.trim();
                const option = document.querySelector(`#actes_datalist option[value="${CSS.escape(val)}"]`);
                if (option) {
                    if (option.dataset.prix) {
                        inputPu.value = option.dataset.prix;
                        recalculerTotaux();
                    }
                    const selectService = document.getElementById('service_id');
                    if (selectService && !selectService.value && option.dataset.serviceId) {
                        selectService.value = option.dataset.serviceId;
                    }
                }
            }

            inputDesignation.addEventListener('input', function() { updateFromDatalist(this); });
            inputDesignation.addEventListener('change', function() { updateFromDatalist(this); });
        }

        const btnRemove = row.querySelector('.btn-remove-row');
        btnRemove.addEventListener('click', function() {
            if (tableBody.querySelectorAll('.item-row').length > 1) {
                row.remove();
                recalculerTotaux();
            } else {
                alert('Le ticket doit comporter au moins une ligne.');
            }
        });
    }

    // Attacher les événements sur les lignes existantes
    tableBody.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));

    // Bouton d'ajout de ligne
    const btnAddRow = document.getElementById('btn_add_row');
    if (btnAddRow) {
        btnAddRow.addEventListener('click', () => ajouterLigne('acte', '', 0));
    }

    // Boutons d'ajout rapide
    document.querySelectorAll('.quick-add').forEach(btn => {
        btn.addEventListener('click', function() {
            ajouterLigne(this.dataset.type, this.dataset.name, parseFloat(this.dataset.price));
        });
    });

    // Gestion du taux assurance
    inputTauxAssurance.addEventListener('input', recalculerTotaux);

    selectAssurance.addEventListener('change', function() {
        const opt = selectAssurance.options[selectAssurance.selectedIndex];
        const defaultTaux = opt ? opt.getAttribute('data-taux') : 0;
        inputTauxAssurance.value = defaultTaux || 0;
        recalculerTotaux();
    });

    // Paiement guichet
    inputMontantPaye.addEventListener('input', recalculerTotaux);

    btnPayerTout.addEventListener('click', function() {
        inputMontantPaye.value = hiddenPatient.value;
        recalculerTotaux();
    });

    // ==========================================
    // RECHERCHE DYNAMIQUE DES MÉDECINS
    // ==========================================
    const medecinSearchInput = document.getElementById('medecin_search_input');
    const hiddenMedecinId = document.getElementById('medecin_id');
    const medecinResultsList = document.getElementById('medecin_results_list');
    const btnClearMedecin = document.getElementById('btn_clear_medecin');
    const btnToggleMedecins = document.getElementById('btn_toggle_medecins_dropdown');

@php
    $medecinsJson = json_encode($medecins->map(function($m) {
        return [
            'id' => $m->id,
            'nom' => $m->nom,
            'prenom' => $m->prenom,
            'nom_complet' => 'Dr ' . $m->nom . ' ' . $m->prenom,
            'specialite' => $m->specialite ?: 'Généraliste',
        ];
    }));
@endphp
    const medecinsData = {!! $medecinsJson !!};

    function escapeHtmlText(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderMedecins(filterText = '') {
        if (!medecinResultsList) return;
        medecinResultsList.innerHTML = '';
        const term = (filterText || '').trim().toLowerCase();

        // Option 0 : Tout Médecin / Non assigné
        const itemNone = document.createElement('button');
        itemNone.type = 'button';
        itemNone.className = 'list-group-item list-group-item-action py-2 px-3 text-muted small fst-italic border-bottom';
        itemNone.innerHTML = '<span>-- Tout Médecin / Non assigné --</span>';
        itemNone.addEventListener('click', function() {
            hiddenMedecinId.value = '';
            medecinSearchInput.value = '';
            if (btnClearMedecin) btnClearMedecin.style.display = 'none';
            medecinResultsList.style.display = 'none';
        });
        medecinResultsList.appendChild(itemNone);

        const filtered = medecinsData.filter(m => {
            if (!term) return true;
            return m.nom.toLowerCase().includes(term)
                || m.prenom.toLowerCase().includes(term)
                || m.nom_complet.toLowerCase().includes(term)
                || m.specialite.toLowerCase().includes(term);
        });

        if (filtered.length === 0) {
            const noRes = document.createElement('div');
            noRes.className = 'list-group-item py-2 px-3 text-muted small';
            noRes.textContent = 'Aucun médecin correspondant trouvé.';
            medecinResultsList.appendChild(noRes);
        } else {
            filtered.forEach(m => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3';
                item.innerHTML = `
                    <div>
                        <span class="fw-semibold text-dark">${escapeHtmlText(m.nom_complet)}</span>
                    </div>
                    <span class="badge badge-info-pill">${escapeHtmlText(m.specialite)}</span>
                `;
                item.addEventListener('click', function() {
                    hiddenMedecinId.value = m.id;
                    medecinSearchInput.value = `${m.nom_complet} (${m.specialite})`;
                    if (btnClearMedecin) btnClearMedecin.style.display = 'inline-block';
                    medecinResultsList.style.display = 'none';
                });
                medecinResultsList.appendChild(item);
            });
        }

        medecinResultsList.style.display = 'block';
    }

    if (medecinSearchInput) {
        medecinSearchInput.addEventListener('input', function() {
            renderMedecins(this.value);
            if (!this.value.trim()) {
                hiddenMedecinId.value = '';
                if (btnClearMedecin) btnClearMedecin.style.display = 'none';
            }
        });

        medecinSearchInput.addEventListener('focus', function() {
            renderMedecins(this.value);
        });
    }

    if (btnToggleMedecins) {
        btnToggleMedecins.addEventListener('click', function(e) {
            e.stopPropagation();
            if (medecinResultsList.style.display === 'block') {
                medecinResultsList.style.display = 'none';
            } else {
                renderMedecins('');
                medecinSearchInput.focus();
            }
        });
    }

    if (btnClearMedecin) {
        btnClearMedecin.addEventListener('click', function() {
            hiddenMedecinId.value = '';
            medecinSearchInput.value = '';
            this.style.display = 'none';
            medecinResultsList.style.display = 'none';
        });
    }

    document.addEventListener('click', function(event) {
        if (medecinSearchInput && medecinResultsList) {
            if (!medecinSearchInput.contains(event.target) && !medecinResultsList.contains(event.target) && (!btnToggleMedecins || !btnToggleMedecins.contains(event.target))) {
                medecinResultsList.style.display = 'none';
            }
        }
    });

    // ==========================================
    // GESTION MODALE CRÉATION RAPIDE DE PATIENT
    // ==========================================
    const quickPatientModalEl = document.getElementById('quickPatientModal');
    const quickPatientForm = document.getElementById('quick_patient_form');
    const radioNonAssure = document.getElementById('qp_statut_non_assure');
    const radioAssure = document.getElementById('qp_statut_assure');
    const qpAssuranceFields = document.getElementById('qp_assurance_fields');
    const qpAssuranceSelect = document.getElementById('qp_assurance_id');
    const qpNumeroAssure = document.getElementById('qp_numero_assure');
    const qpTauxInput = document.getElementById('qp_taux_couverture');
    const qpAlert = document.getElementById('quick_patient_alert');
    const qpBtnSubmit = document.getElementById('btn_submit_quick_patient');
    const qpSpinner = document.getElementById('qp_spinner');
    const qpBtnIcon = document.getElementById('qp_btn_icon');

    function updateModalAssuranceVisibility() {
        if (radioAssure && radioAssure.checked) {
            if (qpAssuranceFields) qpAssuranceFields.classList.remove('d-none');
            if (qpNumeroAssure) qpNumeroAssure.setAttribute('required', 'required');
            if (qpAssuranceSelect) qpAssuranceSelect.setAttribute('required', 'required');
        } else {
            if (qpAssuranceFields) qpAssuranceFields.classList.add('d-none');
            if (qpNumeroAssure) qpNumeroAssure.removeAttribute('required');
            if (qpAssuranceSelect) qpAssuranceSelect.removeAttribute('required');
        }
    }

    if (radioNonAssure && radioAssure) {
        radioNonAssure.addEventListener('change', updateModalAssuranceVisibility);
        radioAssure.addEventListener('change', updateModalAssuranceVisibility);
    }

    if (qpAssuranceSelect && qpTauxInput) {
        qpAssuranceSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const taux = opt ? opt.getAttribute('data-taux') : 0;
            qpTauxInput.value = taux || 80;
        });
    }

    if (quickPatientForm) {
        quickPatientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (qpAlert) {
                qpAlert.classList.add('d-none');
                qpAlert.innerHTML = '';
            }
            if (qpBtnSubmit) qpBtnSubmit.disabled = true;
            if (qpSpinner) qpSpinner.classList.remove('d-none');
            if (qpBtnIcon) qpBtnIcon.classList.add('d-none');

            const formData = new FormData(quickPatientForm);

            fetch(quickPatientForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    let errMsg = data.message || 'Erreur lors de la création du patient.';
                    if (data.errors) {
                        const errList = Object.values(data.errors).flat().join('<br>');
                        errMsg = `<strong>Erreur de validation :</strong><br>${errList}`;
                    }
                    throw new Error(errMsg);
                }
                return data;
            })
            .then(data => {
                if (data.success && data.patient) {
                    if (typeof window.selectPatientInForm === 'function') {
                        window.selectPatientInForm(data.patient);
                    }

                    // Fermeture de la modale
                    if (window.bootstrap && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(quickPatientModalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }

                    // Message visuel sous le champ patient
                    const badgeSuccess = document.getElementById('quick_patient_success_badge');
                    const textSuccess = document.getElementById('quick_patient_success_text');
                    if (badgeSuccess && textSuccess) {
                        textSuccess.textContent = `Patient « ${data.patient.nom_complet} » enregistré et sélectionné avec succès !`;
                        badgeSuccess.classList.remove('d-none');
                        setTimeout(() => badgeSuccess.classList.add('d-none'), 6000);
                    }

                    quickPatientForm.reset();
                    if (radioNonAssure) {
                        radioNonAssure.checked = true;
                    }
                    updateModalAssuranceVisibility();
                }
            })
            .catch(error => {
                if (qpAlert) {
                    qpAlert.innerHTML = error.message;
                    qpAlert.classList.remove('d-none');
                }
            })
            .finally(() => {
                if (qpBtnSubmit) qpBtnSubmit.disabled = false;
                if (qpSpinner) qpSpinner.classList.add('d-none');
                if (qpBtnIcon) qpBtnIcon.classList.remove('d-none');
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        });
    }

    // Initialisation
    recalculerTotaux();
});
</script>
@endpush

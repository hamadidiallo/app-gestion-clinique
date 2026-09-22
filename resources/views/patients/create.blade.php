@extends('layout')

@section('title', 'Nouveau Patient - ' . (auth()->user()->clinique->nom ?? config('app.name')))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page moderne avec fil d'Ariane --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}" class="text-decoration-none text-muted">Patients</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Nouveau Dossier</li>
                </ol>
            </nav>
            <h1 class="h3 text-dark fw-bolder mb-1 d-flex align-items-center gap-2">
                <span class="p-2 bg-primary-subtle text-primary rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i data-lucide="user-plus" class="fs-5"></i>
                </span>
                Admission & Création de Patient
            </h1>
            <p class="text-muted mb-0 small">Enregistrement administratif du patient, affiliation assurance et initialisation du dossier médical.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                <i data-lucide="arrow-left" class="me-1"></i> Répertoire Patients
            </a>
        </div>
    </div>

    {{-- Affichage des alertes d'erreurs éventuelles --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3 d-flex align-items-start gap-3">
            <i data-lucide="alert-octagon" class="fs-4 text-danger mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">Veuillez corriger les informations suivantes :</h6>
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('patients.store') }}" method="POST" id="patient_form">
        @csrf
        <input type="hidden" name="action" id="form_action" value="save">

        <div class="row g-4">
            {{-- COLONNE GAUCHE : FORMULAIRE PRINCIPAL (8 COLS) --}}
            <div class="col-lg-8">
                
                {{-- 1. IDENTITÉ & CONTACT --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                        <div class="badge bg-primary text-white rounded-pill px-2 py-1 small">Étape 1</div>
                        <h5 class="mb-0 fw-bold text-dark fs-6">État Civil & Coordonnées</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Prénom --}}
                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-bold text-dark small mb-1">
                                    Prénom(s) du Patient <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">
                                        <i data-lucide="user"></i>
                                    </span>
                                    <input type="text" 
                                           name="prenom" 
                                           id="prenom" 
                                           class="form-control form-control-lg fs-6 border-start-0 rounded-end-3 @error('prenom') is-invalid @enderror" 
                                           placeholder="Ex: Amadou" 
                                           value="{{ old('prenom') }}" 
                                           required 
                                           autocomplete="given-name">
                                </div>
                                @error('prenom')
                                    <div class="text-danger small mt-1"><i data-lucide="x-circle" class="me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nom --}}
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-bold text-dark small mb-1">
                                    Nom de Famille <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">
                                        <i data-lucide="user"></i>
                                    </span>
                                    <input type="text" 
                                           name="nom" 
                                           id="nom" 
                                           class="form-control form-control-lg fs-6 border-start-0 rounded-end-3 text-uppercase @error('nom') is-invalid @enderror" 
                                           placeholder="Ex: DIALLO" 
                                           value="{{ old('nom') }}" 
                                           required 
                                           autocomplete="family-name">
                                </div>
                                @error('nom')
                                    <div class="text-danger small mt-1"><i data-lucide="x-circle" class="me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sexe / Genre avec Boutons Tuiles Interactifs --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-2">
                                    Genre / Sexe <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexe" id="sexe_m" value="M" {{ old('sexe', 'M') === 'M' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 p-3 rounded-3 d-flex flex-column align-items-center gap-1 gender-tile" for="sexe_m">
                                            <i data-lucide="user" class="fs-4 text-primary"></i>
                                            <span class="fw-bold fs-7">Masculin (H)</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexe" id="sexe_f" value="F" {{ old('sexe') === 'F' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger w-100 p-3 rounded-3 d-flex flex-column align-items-center gap-1 gender-tile" for="sexe_f">
                                            <i data-lucide="user" class="fs-4 text-danger"></i>
                                            <span class="fw-bold fs-7">Féminin (F)</span>
                                        </label>
                                    </div>
                                </div>
                                @error('sexe')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-bold text-dark small mb-1">
                                    Numéro de Téléphone
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">
                                        <i data-lucide="phone"></i>
                                    </span>
                                    <input type="tel" 
                                           name="telephone" 
                                           id="telephone" 
                                           class="form-control form-control-lg fs-6 border-start-0 rounded-end-3 @error('telephone') is-invalid @enderror" 
                                           placeholder="Ex: 70 12 34 56 ou +223 ..." 
                                           value="{{ old('telephone') }}">
                                </div>
                                <div class="form-text small text-muted">Numéro direct du patient ou de son accompagnant légal.</div>
                                @error('telephone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. STATUT D'ASSURANCE & TIERS PAYANT --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                        <div class="badge bg-primary text-white rounded-pill px-2 py-1 small">Étape 2</div>
                        <h5 class="mb-0 fw-bold text-dark fs-6">Couverture Maladie & Facturation</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-bold text-dark small mb-2">Mode de Prise en Charge <span class="text-danger">*</span></label>
                        
                        {{-- Tuiles de Sélection du Statut --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="statut" id="statut_non_assure" value="non_assure" {{ old('statut', 'non_assure') === 'non_assure' ? 'checked' : '' }}>
                                <label class="card h-100 p-3 rounded-3 border-2 cursor-pointer status-tile text-start" for="statut_non_assure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="p-2 rounded-circle bg-light text-secondary d-inline-flex">
                                            <i data-lucide="wallet" class="fs-5"></i>
                                        </div>
                                        <span class="status-check-badge badge bg-secondary-subtle text-secondary rounded-pill">Sélectionné</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Paiement Direct (Privé)</h6>
                                    <p class="small text-muted mb-0">Règlement intégral (100%) des actes, médicaments et soins en caisse par le patient.</p>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="statut" id="statut_assure" value="assure" {{ old('statut') === 'assure' ? 'checked' : '' }}>
                                <label class="card h-100 p-3 rounded-3 border-2 cursor-pointer status-tile text-start" for="statut_assure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="p-2 rounded-circle bg-primary-subtle text-primary d-inline-flex">
                                            <i data-lucide="shield-check" class="fs-5"></i>
                                        </div>
                                        <span class="status-check-badge badge bg-primary-subtle text-primary rounded-pill">Tiers Payant</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-primary">Assuré / Tiers Payant</h6>
                                    <p class="small text-muted mb-0">Couverture partielle ou totale (AMO, INPS, Assurances privées du Mali).</p>
                                </label>
                            </div>
                        </div>

                        {{-- Conteneur Dynamique Assurance (Affiché si statut = assure) --}}
                        <div id="assurance_fields_box" class="p-4 rounded-4 border bg-light-subtle shadow-sm mb-2" style="display: {{ old('statut') === 'assure' ? 'block' : 'none' }};">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i data-lucide="list-checks"></i> Paramètres de la Carte / Police d'Assurance
                                </h6>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 font-monospace small">Tiers Payant</span>
                            </div>

                            {{-- Boutons d'accès rapide aux organismes récurrents --}}
                            <div class="mb-3">
                                <span class="small fw-semibold text-muted me-2">Sélection rapide :</span>
                                @foreach($assurances->take(4) as $assQuick)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 me-1 mb-1 quick-assurance-btn"
                                            data-id="{{ $assQuick->id }}" 
                                            data-taux="{{ $assQuick->taux_par_defaut ?? 80 }}">
                                        {{ $assQuick->nom }} ({{ number_format($assQuick->taux_par_defaut ?? 80, 0) }}%)
                                    </button>
                                @endforeach
                            </div>

                            <div class="row g-3">
                                {{-- Organisme d'Assurance --}}
                                <div class="col-md-6">
                                    <label for="assurance_id" class="form-label fw-bold text-dark small mb-1">
                                        Compagnie ou Organisme d'Assurance
                                    </label>
                                    <select name="assurance_id" id="assurance_id" class="form-select form-select-lg fs-6 rounded-3 @error('assurance_id') is-invalid @enderror">
                                        <option value="" data-taux="80">-- Sélectionner une assurance --</option>
                                        @foreach($assurances as $assurance)
                                            <option value="{{ $assurance->id }}" 
                                                    data-nom="{{ $assurance->nom }}"
                                                    data-code="{{ $assurance->code }}"
                                                    data-taux="{{ $assurance->taux_par_defaut ?? 80 }}" 
                                                    {{ old('assurance_id') == $assurance->id ? 'selected' : '' }}>
                                                {{ $assurance->nom }} {{ $assurance->code ? '('.$assurance->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assurance_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Numéro Matricule / Carte --}}
                                <div class="col-md-6">
                                    <label for="numero_assure" class="form-label fw-bold text-dark small mb-1">
                                        N° d'Assuré / Matricule d'Affiliation
                                    </label>
                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text bg-white text-muted border-end-0 rounded-start-3">
                                            <i data-lucide="hash"></i>
                                        </span>
                                        <input type="text" 
                                               name="numero_assure" 
                                               id="numero_assure" 
                                               class="form-control form-control-lg fs-6 border-start-0 rounded-end-3 @error('numero_assure') is-invalid @enderror" 
                                               placeholder="Ex: AMO-8902341 / INPS-1234" 
                                               value="{{ old('numero_assure') }}">
                                    </div>
                                    @error('numero_assure')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Taux de Couverture avec Jauge interactive --}}
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="taux_couverture" class="form-label fw-bold text-dark small mb-0">
                                            Taux de Prise en Charge par l'Assurance (%)
                                        </label>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold fs-7" id="taux_badge_display">
                                            80% Pris en Charge
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-grow-1">
                                            <input type="range" 
                                                   class="form-range" 
                                                   min="0" 
                                                   max="100" 
                                                   step="5" 
                                                   id="taux_couverture_range" 
                                                   value="{{ old('taux_couverture', 80) }}">
                                        </div>
                                        <div style="width: 110px;">
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       name="taux_couverture" 
                                                       id="taux_couverture" 
                                                       class="form-control fw-bold text-center" 
                                                       min="0" 
                                                       max="100" 
                                                       value="{{ old('taux_couverture', 80) }}">
                                                <span class="input-group-text bg-light fw-bold">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted fs-8 mt-1">
                                        <span>0% (Sans couverture)</span>
                                        <span>70% (Taux standard)</span>
                                        <span>80% (AMO Mali)</span>
                                        <span>100% (Prise en charge intégrale)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. DOSSIER MÉDICAL INITIAL (ACCORDÉON OPTIONNEL) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseMedicalRecord" aria-expanded="false">
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">Optionnel</div>
                            <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                <i data-lucide="heart-pulse" class="text-danger"></i>
                                Dossier Médical Initial & Antécédents
                            </h5>
                        </div>
                        <span class="badge bg-light text-muted border"><i data-lucide="chevron-down"></i> Cliquer pour déplier</span>
                    </div>
                    
                    <div class="collapse" id="collapseMedicalRecord">
                        <div class="card-body p-4 bg-light-subtle">
                            <div class="row g-3">
                                {{-- Groupe Sanguin --}}
                                <div class="col-md-4">
                                    <label for="groupe_sanguin" class="form-label fw-bold text-dark small mb-1">
                                        Groupe Sanguin & Rhésus
                                    </label>
                                    <select name="groupe_sanguin" id="groupe_sanguin" class="form-select rounded-3">
                                        <option value="">-- Non déterminé --</option>
                                        <option value="A+" {{ old('groupe_sanguin') === 'A+' ? 'selected' : '' }}>A Positif (A+)</option>
                                        <option value="A-" {{ old('groupe_sanguin') === 'A-' ? 'selected' : '' }}>A Négatif (A-)</option>
                                        <option value="B+" {{ old('groupe_sanguin') === 'B+' ? 'selected' : '' }}>B Positif (B+)</option>
                                        <option value="B-" {{ old('groupe_sanguin') === 'B-' ? 'selected' : '' }}>B Négatif (B-)</option>
                                        <option value="AB+" {{ old('groupe_sanguin') === 'AB+' ? 'selected' : '' }}>AB Positif (AB+)</option>
                                        <option value="AB-" {{ old('groupe_sanguin') === 'AB-' ? 'selected' : '' }}>AB Négatif (AB-)</option>
                                        <option value="O+" {{ old('groupe_sanguin') === 'O+' ? 'selected' : '' }}>O Positif (O+)</option>
                                        <option value="O-" {{ old('groupe_sanguin') === 'O-' ? 'selected' : '' }}>O Négatif (O-)</option>
                                    </select>
                                </div>

                                {{-- Allergies --}}
                                <div class="col-md-8">
                                    <label for="allergies" class="form-label fw-bold text-danger small mb-1">
                                        <i data-lucide="alert-triangle" class="me-1"></i> Allergies Notables
                                    </label>
                                    <input type="text" 
                                           name="allergies" 
                                           id="allergies" 
                                           class="form-control rounded-3" 
                                           placeholder="Ex: Pénicilline, Bétadine, Aspirine..." 
                                           value="{{ old('allergies') }}">
                                </div>

                                {{-- Antécédents Personnels --}}
                                <div class="col-12">
                                    <label for="antecedents_personnels" class="form-label fw-bold text-dark small mb-1">
                                        Antécédents Médicaux / Pathologies Chroniques
                                    </label>
                                    <textarea name="antecedents_personnels" 
                                              id="antecedents_personnels" 
                                              class="form-control rounded-3" 
                                              rows="2" 
                                              placeholder="Ex: Hypertension artérielle, Diabète de type 2, Asthme...">{{ old('antecedents_personnels') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLONNE DROITE : APERÇU LIVE & ACTIONS (4 COLS) --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 85px;">
                    
                    {{-- CARTE D'IDENTITÉ CLINIQUE (LIVE BADGE) --}}
                    <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 preview-card text-white">
                        <div class="p-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-white-subtle text-white border border-white-subtle px-2 py-1 fs-8 text-uppercase tracking-wider">
                                        {{ auth()->user()->clinique->nom ?? config('app.name') }}
                                    </span>
                                    <div class="fs-8 opacity-75 mt-1">Dossier Patient Numérique</div>
                                </div>
                                <div class="bg-white rounded-circle p-1 shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i data-lucide="building-2" class="text-primary fs-5"></i>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 my-2">
                                <div id="preview_avatar" class="rounded-circle bg-white text-primary fw-bolder d-flex align-items-center justify-content-center shadow" style="width: 58px; height: 58px; font-size: 1.4rem;">
                                    --
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white text-truncate" id="preview_name" style="max-width: 180px;">Nouveau Patient</h5>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-white text-dark rounded-pill fs-8" id="preview_gender">Masculin (H)</span>
                                        <span class="badge bg-white-subtle text-white rounded-pill fs-8" id="preview_phone">Sans téléphone</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Corps inférieur de la carte aperçu --}}
                        <div class="bg-white text-dark p-3 border-top">
                            <div class="p-2 rounded-3 bg-light border mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fs-8 text-muted fw-semibold">Statut Prise en Charge :</span>
                                    <span class="badge bg-secondary-subtle text-secondary" id="preview_status_badge">Privé (100% Patient)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-7">
                                    <span class="fw-bold" id="preview_assurance_name">Aucune assurance</span>
                                    <span class="fw-bold text-primary" id="preview_coverage_val">0%</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div id="preview_progress_bar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between text-muted fs-8 px-1">
                                <span><i data-lucide="map-pin" class="me-1"></i>Kati, Mali</span>
                                <span class="font-monospace text-primary fw-bold" id="preview_matricule">N° Carte: --</span>
                            </div>
                        </div>
                    </div>

                    {{-- BOÎTIER DE SOUMISSION / ACTIONS --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                        <h6 class="fw-bold text-dark mb-3"><i data-lucide="send-check" class="text-primary me-2"></i>Actions d'Enregistrement</h6>
                        
                        <div class="d-grid gap-2">
                            {{-- Bouton 1 : Enregistrer et Créer Ticket directement --}}
                            <button type="button" class="btn btn-primary btn-lg rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" id="btn_save_and_ticket">
                                <i data-lucide="receipt-cutoff" class="fs-5"></i>
                                <span>Créer & Émettre un Ticket</span>
                            </button>

                            {{-- Bouton 2 : Enregistrement simple --}}
                            <button type="submit" class="btn btn-light border btn-lg rounded-3 fw-semibold text-dark d-flex align-items-center justify-content-center gap-2" id="btn_save_only">
                                <i data-lucide="check-circle" class="fs-5 text-success"></i>
                                <span>Enregistrer le Dossier Seul</span>
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('patients.index') }}" class="text-muted text-decoration-none small">
                                <i data-lucide="x" class="me-1"></i>Annuler et revenir à la liste
                            </a>
                        </div>
                    </div>

                    {{-- CARTE D'AIDE CONTEXTUELLE --}}
                    <div class="card border-0 bg-primary-subtle rounded-4 p-3 border border-primary-subtle text-primary-emphasis">
                        <div class="d-flex align-items-start gap-2">
                            <i data-lucide="info" class="text-primary fs-5 mt-1"></i>
                            <div class="small">
                                <strong>Bonne pratique clinique :</strong>
                                Pour les patients assurés par l'<strong>AMO</strong> ou l'<strong>INPS</strong>, saisissez soigneusement leur numéro de matricule afin de garantir la recevabilité des bordereaux de remboursement.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Design spécifique pour les tuiles de sélection */
    .gender-tile {
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #e2e8f0;
        background-color: #ffffff;
    }
    .gender-tile:hover {
        border-color: #0284c7;
        background-color: #f0f9ff;
        transform: translateY(-2px);
    }
    .btn-check:checked + .gender-tile {
        border-color: #0284c7;
        background-color: #f0f9ff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
    }
    .btn-check:checked + label[for="sexe_f"] {
        border-color: #e11d48 !important;
        background-color: #fff1f2 !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.15) !important;
    }

    .status-tile {
        border: 2px solid #e2e8f0;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    .status-tile:hover {
        border-color: #94a3b8;
        transform: translateY(-2px);
    }
    .btn-check:checked + .status-tile {
        border-color: #0284c7;
        background-color: #f8fafc;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.12);
    }
    .btn-check:not(:checked) + .status-tile .status-check-badge {
        display: none;
    }

    .cursor-pointer {
        cursor: pointer;
    }
    .fs-7 {
        font-size: 0.9rem;
    }
    .fs-8 {
        font-size: 0.78rem;
    }
    .input-group-custom input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments du formulaire
        const inputNom = document.getElementById('nom');
        const inputPrenom = document.getElementById('prenom');
        const inputTelephone = document.getElementById('telephone');
        const radioSexeM = document.getElementById('sexe_m');
        const radioSexeF = document.getElementById('sexe_f');
        const radioStatutAssure = document.getElementById('statut_assure');
        const radioStatutNonAssure = document.getElementById('statut_non_assure');
        const boxAssurance = document.getElementById('assurance_fields_box');
        const selectAssurance = document.getElementById('assurance_id');
        const inputNumeroAssure = document.getElementById('numero_assure');
        const rangeTaux = document.getElementById('taux_couverture_range');
        const inputTaux = document.getElementById('taux_couverture');
        const tauxBadge = document.getElementById('taux_badge_display');

        // Éléments de l'aperçu dynamique (Live Badge)
        const prevAvatar = document.getElementById('preview_avatar');
        const prevName = document.getElementById('preview_name');
        const prevGender = document.getElementById('preview_gender');
        const prevPhone = document.getElementById('preview_phone');
        const prevStatusBadge = document.getElementById('preview_status_badge');
        const prevAssuranceName = document.getElementById('preview_assurance_name');
        const prevCoverageVal = document.getElementById('preview_coverage_val');
        const prevProgressBar = document.getElementById('preview_progress_bar');
        const prevMatricule = document.getElementById('preview_matricule');

        // Boutons de soumission
        const btnSaveAndTicket = document.getElementById('btn_save_and_ticket');
        const btnSaveOnly = document.getElementById('btn_save_only');
        const formAction = document.getElementById('form_action');
        const form = document.getElementById('patient_form');

        // Mise à jour de l'aperçu en temps réel
        function updateLivePreview() {
            const prenom = (inputPrenom?.value || '').trim();
            const nom = (inputNom?.value || '').trim();
            const phone = (inputTelephone?.value || '').trim();
            const isFemale = radioSexeF?.checked;
            const isAssure = radioStatutAssure?.checked;
            const matricule = (inputNumeroAssure?.value || '').trim();
            const taux = parseFloat(inputTaux?.value || 0);

            // Nom complet & Avatar initiales
            if (prenom || nom) {
                prevName.textContent = (prenom + ' ' + nom.toUpperCase()).trim();
                const initP = prenom.charAt(0).toUpperCase();
                const initN = nom.charAt(0).toUpperCase();
                prevAvatar.textContent = (initP + initN) || '--';
            } else {
                prevName.textContent = 'Nouveau Patient';
                prevAvatar.textContent = '--';
            }

            // Genre
            if (isFemale) {
                prevGender.textContent = 'Féminin (F)';
                prevGender.className = 'badge bg-danger-subtle text-danger rounded-pill fs-8';
                prevAvatar.style.color = '#e11d48';
            } else {
                prevGender.textContent = 'Masculin (H)';
                prevGender.className = 'badge bg-white text-primary rounded-pill fs-8';
                prevAvatar.style.color = '#0284c7';
            }

            // Téléphone
            if (phone) {
                prevPhone.textContent = phone;
                prevPhone.className = 'badge bg-white text-dark rounded-pill fs-8';
            } else {
                prevPhone.textContent = 'Sans téléphone';
                prevPhone.className = 'badge bg-white-subtle text-white rounded-pill fs-8';
            }

            // Statut de couverture
            if (isAssure) {
                const opt = selectAssurance.options[selectAssurance.selectedIndex];
                const assName = opt && opt.value ? (opt.getAttribute('data-nom') || opt.text) : 'Assurance sélectionnée';
                prevStatusBadge.textContent = 'Assuré (Tiers Payant)';
                prevStatusBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle';
                prevAssuranceName.textContent = assName;
                prevCoverageVal.textContent = taux + '%';
                prevProgressBar.style.width = Math.min(100, Math.max(0, taux)) + '%';
                prevMatricule.textContent = matricule ? ('N°: ' + matricule) : 'N° Carte: En attente';
            } else {
                prevStatusBadge.textContent = 'Privé (Paiement Direct)';
                prevStatusBadge.className = 'badge bg-secondary-subtle text-secondary border';
                prevAssuranceName.textContent = '100% à la charge du patient';
                prevCoverageVal.textContent = '0%';
                prevProgressBar.style.width = '0%';
                prevMatricule.textContent = 'Sans carte tiers-payant';
            }
        }

        // Bascule de l'affichage de l'assurance
        function toggleAssuranceBox() {
            if (radioStatutAssure.checked) {
                boxAssurance.style.display = 'block';
            } else {
                boxAssurance.style.display = 'none';
            }
            updateLivePreview();
        }

        radioStatutAssure.addEventListener('change', toggleAssuranceBox);
        radioStatutNonAssure.addEventListener('change', toggleAssuranceBox);

        // Synchronisation du Slider et de l'Input Numérique du Taux
        if (rangeTaux && inputTaux) {
            rangeTaux.addEventListener('input', function() {
                inputTaux.value = this.value;
                tauxBadge.textContent = this.value + '% Pris en Charge';
                updateLivePreview();
            });

            inputTaux.addEventListener('input', function() {
                rangeTaux.value = this.value;
                tauxBadge.textContent = this.value + '% Pris en Charge';
                updateLivePreview();
            });
        }

        // Sélection automatique du taux selon l'organisme choisi
        if (selectAssurance) {
            selectAssurance.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const defTaux = opt.getAttribute('data-taux');
                if (defTaux !== null && defTaux !== '') {
                    inputTaux.value = defTaux;
                    if (rangeTaux) rangeTaux.value = defTaux;
                    if (tauxBadge) tauxBadge.textContent = defTaux + '% Pris en Charge';
                }
                updateLivePreview();
            });
        }

        // Boutons de sélection rapide d'assurance
        document.querySelectorAll('.quick-assurance-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const taux = this.getAttribute('data-taux');
                if (selectAssurance) {
                    selectAssurance.value = id;
                }
                if (inputTaux) {
                    inputTaux.value = taux;
                    if (rangeTaux) rangeTaux.value = taux;
                    if (tauxBadge) tauxBadge.textContent = taux + '% Pris en Charge';
                }
                updateLivePreview();
            });
        });

        // Écouteurs de frappe pour la carte en direct
        [inputNom, inputPrenom, inputTelephone, inputNumeroAssure].forEach(el => {
            if (el) {
                el.addEventListener('input', updateLivePreview);
                el.addEventListener('keyup', updateLivePreview);
            }
        });
        [radioSexeM, radioSexeF].forEach(el => {
            if (el) el.addEventListener('change', updateLivePreview);
        });

        // Gestion de l'action de soumission
        if (btnSaveAndTicket) {
            btnSaveAndTicket.addEventListener('click', function() {
                formAction.value = 'save_and_ticket';
                form.submit();
            });
        }
        if (btnSaveOnly) {
            btnSaveOnly.addEventListener('click', function() {
                formAction.value = 'save';
            });
        }

        // Initialisation immédiate
        toggleAssuranceBox();
        updateLivePreview();
    });
</script>
@endpush
@endsection

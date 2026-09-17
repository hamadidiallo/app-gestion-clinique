@extends('layout')

@section('title', 'Modifier le Dossier Patient - Clinique Gahambani')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec fil d'Ariane --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}" class="text-decoration-none text-muted">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}" class="text-decoration-none text-muted">{{ $patient->prenom }} {{ $patient->nom }}</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Modification</li>
                </ol>
            </nav>
            <h1 class="h3 text-dark fw-bolder mb-1 d-flex align-items-center gap-2">
                <span class="p-2 bg-amber-subtle text-amber-700 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: #fef3c7; color: #b45309;">
                    <i data-lucide="edit-3" class="fs-5"></i>
                </span>
                Modifier Dossier : {{ $patient->prenom }} {{ $patient->nom }}
            </h1>
            <p class="text-muted mb-0 small">Mise à jour des informations administratives et de couverture maladie.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                <i data-lucide="eye" class="me-1"></i> Consulter le Dossier
            </a>
            <a href="{{ route('patients.index') }}" class="btn btn-light border px-3 py-2 rounded-3 fw-semibold text-muted">
                <i data-lucide="arrow-left" class="me-1"></i> Répertoire
            </a>
        </div>
    </div>

    {{-- Affichage des alertes d'erreurs éventuelles --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3 d-flex align-items-start gap-3">
            <i data-lucide="alert-octagon" class="fs-4 text-danger mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">Veuillez vérifier les champs suivants :</h6>
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('patients.update', $patient) }}" method="POST" id="patient_edit_form">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- COLONNE GAUCHE (8 COLS) --}}
            <div class="col-lg-8">
                
                {{-- 1. ÉTAT CIVIL & CONTACT --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                        <div class="badge bg-primary text-white rounded-pill px-2 py-1 small">Section 1</div>
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
                                           value="{{ old('prenom', $patient->prenom) }}" 
                                           required>
                                </div>
                                @error('prenom')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                                           value="{{ old('nom', $patient->nom) }}" 
                                           required>
                                </div>
                                @error('nom')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sexe / Genre --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-2">
                                    Genre / Sexe <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexe" id="sexe_m" value="M" {{ old('sexe', $patient->sexe) === 'M' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 p-3 rounded-3 d-flex flex-column align-items-center gap-1 gender-tile" for="sexe_m">
                                            <i data-lucide="user" class="fs-4 text-primary"></i>
                                            <span class="fw-bold fs-7">Masculin (H)</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="sexe" id="sexe_f" value="F" {{ old('sexe', $patient->sexe) === 'F' ? 'checked' : '' }}>
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
                                           value="{{ old('telephone', $patient->telephone) }}">
                                </div>
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
                        <div class="badge bg-primary text-white rounded-pill px-2 py-1 small">Section 2</div>
                        <h5 class="mb-0 fw-bold text-dark fs-6">Couverture Maladie & Facturation</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-bold text-dark small mb-2">Mode de Prise en Charge <span class="text-danger">*</span></label>
                        
                        {{-- Tuiles de Sélection du Statut --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="statut" id="statut_non_assure" value="non_assure" {{ old('statut', $patient->statut) === 'non_assure' ? 'checked' : '' }}>
                                <label class="card h-100 p-3 rounded-3 border-2 cursor-pointer status-tile text-start" for="statut_non_assure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="p-2 rounded-circle bg-light text-secondary d-inline-flex">
                                            <i data-lucide="wallet" class="fs-5"></i>
                                        </div>
                                        <span class="status-check-badge badge bg-secondary-subtle text-secondary rounded-pill">Actif</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">Paiement Direct (Privé)</h6>
                                    <p class="small text-muted mb-0">Règlement intégral (100%) des prestations en caisse par le patient.</p>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="statut" id="statut_assure" value="assure" {{ old('statut', $patient->statut) === 'assure' ? 'checked' : '' }}>
                                <label class="card h-100 p-3 rounded-3 border-2 cursor-pointer status-tile text-start" for="statut_assure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="p-2 rounded-circle bg-primary-subtle text-primary d-inline-flex">
                                            <i data-lucide="shield-check" class="fs-5"></i>
                                        </div>
                                        <span class="status-check-badge badge bg-primary-subtle text-primary rounded-pill">Tiers Payant</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-primary">Assuré / Tiers Payant</h6>
                                    <p class="small text-muted mb-0">Couverture AMO, INPS ou assurance privée partenaire.</p>
                                </label>
                            </div>
                        </div>

                        {{-- Conteneur Dynamique Assurance --}}
                        <div id="assurance_fields_box" class="p-4 rounded-4 border bg-light-subtle shadow-sm mb-2" style="display: {{ old('statut', $patient->statut) === 'assure' ? 'block' : 'none' }};">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i data-lucide="list-checks"></i> Paramètres de la Carte / Police d'Assurance
                                </h6>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 font-monospace small">Tiers Payant</span>
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
                                                    data-taux="{{ $assurance->taux_par_defaut ?? 80 }}" 
                                                    {{ old('assurance_id', $patient->assurance_id ?? ($carteAssurance->assurance_id ?? '')) == $assurance->id ? 'selected' : '' }}>
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
                                               value="{{ old('numero_assure', $patient->numero_assure ?? ($carteAssurance->reference ?? '')) }}"
                                               placeholder="Ex: AMO-8902341 / INPS-1234">
                                    </div>
                                    @error('numero_assure')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Taux de Couverture --}}
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="taux_couverture" class="form-label fw-bold text-dark small mb-0">
                                            Taux de Prise en Charge par l'Assurance (%)
                                        </label>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold fs-7" id="taux_badge_display">
                                            {{ old('taux_couverture', $patient->taux_couverture ?? ($carteAssurance->taux_couverture ?? 80)) }}% Pris en Charge
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
                                                   value="{{ old('taux_couverture', $patient->taux_couverture ?? ($carteAssurance->taux_couverture ?? 80)) }}">
                                        </div>
                                        <div style="width: 110px;">
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       name="taux_couverture" 
                                                       id="taux_couverture" 
                                                       class="form-control fw-bold text-center" 
                                                       min="0" 
                                                       max="100" 
                                                       value="{{ old('taux_couverture', $patient->taux_couverture ?? ($carteAssurance->taux_couverture ?? 80)) }}">
                                                <span class="input-group-text bg-light fw-bold">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLONNE DROITE (4 COLS) --}}
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 85px;">
                    
                    {{-- CARTE APERÇU LIVE --}}
                    <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 text-white">
                        <div class="p-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-white-subtle text-white border border-white-subtle px-2 py-1 fs-8 text-uppercase tracking-wider">
                                        Clinique Gahambani
                                    </span>
                                    <div class="fs-8 opacity-75 mt-1">Dossier #{{ $patient->id }}</div>
                                </div>
                                <div class="bg-white rounded-circle p-1 shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i data-lucide="building-2" class="text-primary fs-5"></i>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 my-2">
                                <div id="preview_avatar" class="rounded-circle bg-white text-primary fw-bolder d-flex align-items-center justify-content-center shadow" style="width: 58px; height: 58px; font-size: 1.4rem;">
                                    {{ strtoupper(substr($patient->prenom, 0, 1) . substr($patient->nom, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white text-truncate" id="preview_name" style="max-width: 180px;">
                                        {{ $patient->prenom }} {{ strtoupper($patient->nom) }}
                                    </h5>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-white text-dark rounded-pill fs-8" id="preview_gender">
                                            {{ $patient->sexe === 'F' ? 'Féminin (F)' : 'Masculin (H)' }}
                                        </span>
                                        <span class="badge bg-white-subtle text-white rounded-pill fs-8" id="preview_phone">
                                            {{ $patient->telephone ?? 'Sans tél' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white text-dark p-3 border-top">
                            <div class="p-2 rounded-3 bg-light border mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fs-8 text-muted fw-semibold">Statut Prise en Charge :</span>
                                    <span class="badge bg-primary-subtle text-primary" id="preview_status_badge">
                                        {{ $patient->statut === 'assure' ? 'Assuré (Tiers Payant)' : 'Privé' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-7">
                                    <span class="fw-bold" id="preview_assurance_name">{{ $patient->assurance?->nom ?? 'Aucune' }}</span>
                                    <span class="fw-bold text-primary" id="preview_coverage_val">{{ $patient->taux_couverture ?? 0 }}%</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div id="preview_progress_bar" class="progress-bar bg-primary" role="progressbar" style="width: {{ $patient->taux_couverture ?? 0 }}%;"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between text-muted fs-8 px-1">
                                <span>Inscrit le {{ $patient->created_at ? $patient->created_at->format('d/m/Y') : '--' }}</span>
                                <span class="font-monospace text-primary fw-bold" id="preview_matricule">N°: {{ $patient->numero_assure ?? '--' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- BOUTONS D'ACTION --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                        <h6 class="fw-bold text-dark mb-3"><i data-lucide="save2" class="text-primary me-2"></i>Validation des Modifications</h6>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i data-lucide="check-circle" class="fs-5"></i>
                                <span>Enregistrer les Modifications</span>
                            </button>
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-light border rounded-3 fw-semibold text-muted text-center py-2">
                                Annuler
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<style>
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

    .cursor-pointer { cursor: pointer; }
    .fs-7 { font-size: 0.9rem; }
    .fs-8 { font-size: 0.78rem; }
    .input-group-custom input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        const prevAvatar = document.getElementById('preview_avatar');
        const prevName = document.getElementById('preview_name');
        const prevGender = document.getElementById('preview_gender');
        const prevPhone = document.getElementById('preview_phone');
        const prevStatusBadge = document.getElementById('preview_status_badge');
        const prevAssuranceName = document.getElementById('preview_assurance_name');
        const prevCoverageVal = document.getElementById('preview_coverage_val');
        const prevProgressBar = document.getElementById('preview_progress_bar');
        const prevMatricule = document.getElementById('preview_matricule');

        function updateLivePreview() {
            const prenom = (inputPrenom?.value || '').trim();
            const nom = (inputNom?.value || '').trim();
            const phone = (inputTelephone?.value || '').trim();
            const isFemale = radioSexeF?.checked;
            const isAssure = radioStatutAssure?.checked;
            const matricule = (inputNumeroAssure?.value || '').trim();
            const taux = parseFloat(inputTaux?.value || 0);

            if (prenom || nom) {
                prevName.textContent = (prenom + ' ' + nom.toUpperCase()).trim();
                const initP = prenom.charAt(0).toUpperCase();
                const initN = nom.charAt(0).toUpperCase();
                prevAvatar.textContent = (initP + initN) || '--';
            }

            if (isFemale) {
                prevGender.textContent = 'Féminin (F)';
                prevGender.className = 'badge bg-danger-subtle text-danger rounded-pill fs-8';
                prevAvatar.style.color = '#e11d48';
            } else {
                prevGender.textContent = 'Masculin (H)';
                prevGender.className = 'badge bg-white text-primary rounded-pill fs-8';
                prevAvatar.style.color = '#0284c7';
            }

            prevPhone.textContent = phone || 'Sans téléphone';

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

        [inputNom, inputPrenom, inputTelephone, inputNumeroAssure].forEach(el => {
            if (el) {
                el.addEventListener('input', updateLivePreview);
                el.addEventListener('keyup', updateLivePreview);
            }
        });
        [radioSexeM, radioSexeF].forEach(el => {
            if (el) el.addEventListener('change', updateLivePreview);
        });

        toggleAssuranceBox();
        updateLivePreview();
    });
</script>
@endpush
@endsection

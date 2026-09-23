@extends('layout')

@section('title', 'Nouvelle Consultation Médicale & Prise de Constantes')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="heart-pulse" class="me-2"></i>Nouvelle Consultation Médicale
            </h1>
            <p class="text-muted mb-0">Relevé des constantes, dossier médical, diagnostic et prescription d'ordonnance.</p>
        </div>
        <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
            <i data-lucide="arrow-left" class="me-1"></i> Retour à la liste
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consultations.store') }}" method="POST" id="consultationForm">
        @csrf

        @if($ticket)
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            <div class="alert alert-primary d-flex align-items-center justify-content-between shadow-sm py-2 px-3 mb-4">
                <div>
                    <i data-lucide="receipt-cutoff" class="fs-4 me-2"></i>
                    <strong>Lié au Ticket N° {{ $ticket->reference }}</strong> |
                    Patient : <strong>{{ $ticket->patient->nom }} {{ $ticket->patient->prenom }}</strong>
                    @if($ticket->medecin)
                        | Médecin assigné : <strong>Dr {{ $ticket->medecin->nom }} {{ $ticket->medecin->prenom }}</strong>
                    @endif
                </div>
                <span class="badge bg-success">Payé / Validé</span>
            </div>
        @endif

        <div class="row g-4">
            {{-- COLONNE GAUCHE: IDENTIFICATION PATIENT & CONSTANTES VITALES --}}
            <div class="col-lg-5">
                {{-- Carte 1: Patient & Médecin --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="user" class="me-2"></i>Patient & Médecin</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 position-relative">
                            @if($selectedPatient)
                                <label class="form-label fw-bold"><i data-lucide="user-check" class="me-1 text-success"></i> Patient Assigné <span class="text-danger">*</span></label>
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ $selectedPatient->id }}">
                                <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $selectedPatient->nom }} {{ $selectedPatient->prenom }}</div>
                                        <small class="text-muted"><i data-lucide="phone" style="width: 12px; height: 12px;"></i> {{ $selectedPatient->telephone ?? 'Sans téléphone' }} | Ref: {{ $selectedPatient->reference }}</small>
                                    </div>
                                    <span class="badge bg-primary">Ticket / Assigné</span>
                                </div>
                            @else
                                @php
                                    $initialPatientName = '';
                                    if (old('patient_id')) {
                                        $oldP = $patients->firstWhere('id', old('patient_id'));
                                        if ($oldP) {
                                            $initialPatientName = $oldP->prenom . ' ' . $oldP->nom . ($oldP->telephone ? ' (' . $oldP->telephone . ')' : '');
                                        }
                                    }
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="patient_search_input" class="form-label fw-bold mb-0">
                                        <i data-lucide="user-search" class="me-1 text-primary"></i> Patient <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="togglePatientModeBtn" style="font-size: 0.78rem;">
                                        <i data-lucide="list" style="width: 13px; height: 13px;"></i> <span id="togglePatientModeText">Choisir dans la liste</span>
                                    </button>
                                </div>

                                {{-- Mode 1 : Recherche Rapide par Nom Complet ou Téléphone (Par défaut) --}}
                                <div id="patient_search_mode_wrapper">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-primary border-end-0">
                                            <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                                        </span>
                                        <input type="text" 
                                               id="patient_search_input" 
                                               class="form-control border-start-0 ps-1 @error('patient_id') is-invalid @enderror" 
                                               placeholder="Tapez le nom complet ou n° de téléphone..." 
                                               data-url="{{ route('patients.search') }}" 
                                               value="{{ $initialPatientName }}" 
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" id="clear_patient_search_btn" title="Effacer la sélection" style="{{ $initialPatientName ? '' : 'display: none;' }}">
                                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i data-lucide="info" style="width: 12px; height: 12px;" class="me-1"></i> Recherche instantanée par nom, prénom ou téléphone (ex: <em>Traoré</em> ou <em>76...</em>).
                                    </small>

                                    {{-- Menu flottant d'autocomplétion des patients --}}
                                    <div id="patient_results_list" class="list-group position-absolute w-100 shadow-lg rounded-3 mt-1" style="display: none; z-index: 1060; max-height: 280px; overflow-y: auto;"></div>
                                </div>

                                {{-- Mode 2 : Liste Déroulante Alternative --}}
                                <div id="patient_dropdown_mode_wrapper" class="d-none">
                                    <select id="patient_select_dropdown" class="form-select @error('patient_id') is-invalid @enderror">
                                        <option value="">-- Sélectionnez un patient dans la liste --</option>
                                        @foreach($patients as $p)
                                            <option value="{{ $p->id }}" 
                                                    data-nom="{{ $p->prenom }} {{ $p->nom }}" 
                                                    data-tel="{{ $p->telephone ?? 'Sans tél' }}"
                                                    @if($peutVoirDossierMedical)
                                                        {{-- Secret medical : ces attributs seraient lisibles dans le
                                                             code de la page, ils ne sont donc emis que pour les soignants --}}
                                                        data-groupe="{{ $p->dossierMedical?->groupe_sanguin ?? '' }}"
                                                        data-allergies="{{ $p->dossierMedical?->allergies ?? '' }}"
                                                        data-ant-perso="{{ $p->dossierMedical?->antecedents_personnels ?? '' }}"
                                                        data-ant-fam="{{ $p->dossierMedical?->antecedents_familiaux ?? '' }}"
                                                    @endif
                                                    {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }} {{ $p->prenom }} ({{ $p->telephone ?? 'Sans tél' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Sélectionnez le patient dans la liste des enregistrements récents.</small>
                                </div>

                                {{-- Champ caché réel pour la soumission du formulaire --}}
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">

                                {{-- Récapitulatif visuel du patient actuellement sélectionné --}}
                                <div id="selected_patient_card" class="mt-2 p-2 bg-success-subtle border border-success-subtle rounded-2 {{ old('patient_id') && $initialPatientName ? '' : 'd-none' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i data-lucide="check-circle-2" class="text-success me-1" style="width: 15px; height: 15px;"></i>
                                            <span class="fw-bold text-success-emphasis" id="selected_patient_name">{{ $initialPatientName }}</span>
                                            <span class="text-muted small ms-2" id="selected_patient_tel"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" id="btn_remove_selected_patient" title="Changer de patient">
                                            <i data-lucide="x-circle" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </div>
                                </div>

                                @error('patient_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="medecin_id" class="form-label fw-bold">Médecin Traitant</label>
                            <select name="medecin_id" id="medecin_id" class="form-select">
                                <option value="">Sélectionnez le médecin...</option>
                                @foreach($medecins as $med)
                                    <option value="{{ $med->id }}" {{ (old('medecin_id', $defaultMedecinId) == $med->id) ? 'selected' : '' }}>
                                        Dr {{ $med->nom }} {{ $med->prenom }} ({{ $med->specialite ?? 'Généraliste' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_consultation" class="form-label fw-bold">Date & Heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_consultation" id="date_consultation" class="form-control" value="{{ old('date_consultation', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>
                </div>

                {{-- Carte 2: Relevé des Constantes Vitales --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="activity" class="me-2"></i>Prise de Constantes Vitales</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Tension Artérielle</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="tension_arterielle" class="form-control @error('tension_arterielle') is-invalid @enderror" placeholder="ex: 12/8" value="{{ old('tension_arterielle') }}">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                                @error('tension_arterielle')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Température</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="temperature" class="form-control @error('temperature') is-invalid @enderror" placeholder="ex: 37.2" value="{{ old('temperature') }}">
                                    <span class="input-group-text">°C</span>
                                </div>
                                @error('temperature')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Poids</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="poids" id="poidsInput" class="form-control @error('poids') is-invalid @enderror" placeholder="ex: 70" value="{{ old('poids') }}">
                                    <span class="input-group-text">kg</span>
                                </div>
                                @error('poids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Taille</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="taille" id="tailleInput" class="form-control @error('taille') is-invalid @enderror" placeholder="ex: 175 (ou 1.75)" value="{{ old('taille') }}">
                                    <span class="input-group-text">cm</span>
                                </div>
                                @error('taille')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Pouls</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="pouls" class="form-control @error('pouls') is-invalid @enderror" placeholder="ex: 75" value="{{ old('pouls') }}">
                                    <span class="input-group-text">bpm</span>
                                </div>
                                @error('pouls')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Glycémie</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="glycemie" class="form-control @error('glycemie') is-invalid @enderror" placeholder="ex: 0.95 (ou 95)" value="{{ old('glycemie') }}">
                                    <span class="input-group-text">g/L</span>
                                </div>
                                @error('glycemie')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">SpO2 (Oxygène)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="saturation_oxygene" class="form-control @error('saturation_oxygene') is-invalid @enderror" placeholder="ex: 98" value="{{ old('saturation_oxygene') }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('saturation_oxygene')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Fréq. Respiratoire</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="frequence_respiratoire" class="form-control @error('frequence_respiratoire') is-invalid @enderror" placeholder="ex: 18" value="{{ old('frequence_respiratoire') }}">
                                    <span class="input-group-text">cpm</span>
                                </div>
                                @error('frequence_respiratoire')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 3: Dossier Médical & Antécédents --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="folder-plus" class="me-2"></i>Dossier Médical & Antécédents</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Groupe Sanguin</label>
                            <select name="groupe_sanguin" class="form-select form-select-sm">
                                <option value="">Inconnu</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gs)
                                    <option value="{{ $gs }}" {{ (old('groupe_sanguin', $selectedPatient?->dossierMedical?->groupe_sanguin) == $gs) ? 'selected' : '' }}>
                                        {{ $gs }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Allergies Connues</label>
                            <input type="text" name="allergies" class="form-control form-control-sm" placeholder="ex: Pénicilline, Sulfamides..." value="{{ old('allergies', $selectedPatient?->dossierMedical?->allergies) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Antécédents Personnels</label>
                            <textarea name="antecedents_personnels" rows="2" class="form-control form-control-sm" placeholder="ex: Diabète type 2, Asthme, Appendicectomie...">{{ old('antecedents_personnels', $selectedPatient?->dossierMedical?->antecedents_personnels) }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Antécédents Familiaux</label>
                            <textarea name="antecedents_familiaux" rows="2" class="form-control form-control-sm" placeholder="ex: Père hypertendu, Mère diabétique...">{{ old('antecedents_familiaux', $selectedPatient?->dossierMedical?->antecedents_familiaux) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE: CONSULTATION & ORDONNANCE --}}
            <div class="col-lg-7">
                {{-- Carte 4: Examen Clinique & Diagnostic --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="stethoscope" class="me-2"></i>Examen Clinique & Diagnostic</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="motif_consultation" class="form-label fw-bold">Motif de consultation <span class="text-danger">*</span></label>
                            <input type="text" name="motif_consultation" id="motif_consultation" class="form-control @error('motif_consultation') is-invalid @enderror" placeholder="ex: Fièvre aiguë, Céphalées intenses, Douleur abdominale..." value="{{ old('motif_consultation') }}" required>
                            @error('motif_consultation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="histoire_maladie" class="form-label fw-bold">Histoire de la maladie / Anamnèse</label>
                            <textarea name="histoire_maladie" id="histoire_maladie" rows="3" class="form-control" placeholder="Description des symptômes, date de début, évolution, traitements antérieurs...">{{ old('histoire_maladie') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="examen_physique" class="form-label fw-bold">Examen Physique / Constatations cliniques</label>
                            <textarea name="examen_physique" id="examen_physique" rows="3" class="form-control" placeholder="Examen général, palpation abdominale, auscultation cardio-pulmonaire, ORL...">{{ old('examen_physique') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnostic" class="form-label fw-bold">Diagnostic Posé / Hypothèse</label>
                            <input type="text" name="diagnostic" id="diagnostic" class="form-control" placeholder="ex: Paludisme simple, Gastro-entérite aiguë, Rhinopharyngite..." value="{{ old('diagnostic') }}">
                        </div>

                        <div class="mb-3">
                            <label for="conduite_a_tenir" class="form-label fw-bold">Conduite à tenir / Recommandations</label>
                            <textarea name="conduite_a_tenir" id="conduite_a_tenir" rows="2" class="form-control" placeholder="Examens complémentaires (NFS, Goutte épaisse), Repos 3 jours, Éviter efforts...">{{ old('conduite_a_tenir') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Carte 5: Prescription d'Ordonnance --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Prescription d'Ordonnance Médicale (Facultative)</h6>
                        <button type="button" class="btn btn-sm btn-light fw-bold" id="addPrescriptionBtn">
                            <i data-lucide="plus-circle" class="me-1"></i> Ajouter Médicament
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Renseignez les médicaments si vous souhaitez prescrire une ordonnance. Laissez vide si aucune ordonnance n'est nécessaire.</p>

                        <div id="prescriptionsContainer">
                            {{-- Ligne 1 par défaut --}}
                            <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-prescription-btn" aria-label="Supprimer"></button>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Médicament (Nom / DCI)</label>
                                        <input type="text" name="prescriptions[0][medicament]" class="form-control form-control-sm" placeholder="ex: Paracétamol ou Artéméther">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Forme & Dosage</label>
                                        <input type="text" name="prescriptions[0][dosage]" class="form-control form-control-sm" placeholder="ex: Cp 500mg, Sirop 125mg">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Durée</label>
                                        <input type="text" name="prescriptions[0][duree]" class="form-control form-control-sm" placeholder="ex: 5 jours">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Posologie & Mode d'emploi</label>
                                        <input type="text" name="prescriptions[0][posologie]" class="form-control form-control-sm" placeholder="ex: 1 comprimé 3 fois par jour après les repas">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Instructions Générales / Recommandations de l'ordonnance</label>
                            <input type="text" name="instructions_generales" class="form-control form-control-sm" placeholder="ex: Boire beaucoup d'eau, revenir en consultation dans 5 jours si persistance de la fièvre">
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="d-flex gap-2 justify-content-end mb-5">
                    <a href="{{ route('consultations.index') }}" class="btn btn-secondary px-4">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm fw-bold">
                        <i data-lucide="check-circle" class="me-1"></i> Enregistrer la Consultation & Ordonnance
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gestion des prescriptions médicamenteuses dynamiques
    let rowIndex = 1;
    const addBtn = document.getElementById('addPrescriptionBtn');
    const container = document.getElementById('prescriptionsContainer');

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            const rowHtml = `
                <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-prescription-btn" aria-label="Supprimer"></button>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Médicament (Nom / DCI)</label>
                            <input type="text" name="prescriptions[${rowIndex}][medicament]" class="form-control form-control-sm" placeholder="ex: Amoxicilline">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Forme & Dosage</label>
                            <input type="text" name="prescriptions[${rowIndex}][dosage]" class="form-control form-control-sm" placeholder="ex: Gélule 500mg">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Durée</label>
                            <input type="text" name="prescriptions[${rowIndex}][duree]" class="form-control form-control-sm" placeholder="ex: 7 jours">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Posologie & Mode d'emploi</label>
                            <input type="text" name="prescriptions[${rowIndex}][posologie]" class="form-control form-control-sm" placeholder="ex: 1 gélule matin, midi et soir">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
            rowIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-prescription-btn')) {
                e.target.closest('.prescription-row').remove();
            }
        });
    }

    // 2. Gestion de la sélection et recherche de patient (Recherche rapide <-> Liste déroulante)
    const toggleBtn = document.getElementById('togglePatientModeBtn');
    const toggleText = document.getElementById('togglePatientModeText');
    const searchWrapper = document.getElementById('patient_search_mode_wrapper');
    const dropdownWrapper = document.getElementById('patient_dropdown_mode_wrapper');
    const dropdownSelect = document.getElementById('patient_select_dropdown');
    const searchInput = document.getElementById('patient_search_input');
    const hiddenPatientId = document.getElementById('patient_id');
    const clearBtn = document.getElementById('clear_patient_search_btn');
    const removePatientBtn = document.getElementById('btn_remove_selected_patient');
    const selectedPatientCard = document.getElementById('selected_patient_card');

    if (toggleBtn && searchWrapper && dropdownWrapper) {
        toggleBtn.addEventListener('click', function() {
            const isSearchVisible = !searchWrapper.classList.contains('d-none');
            if (isSearchVisible) {
                searchWrapper.classList.add('d-none');
                dropdownWrapper.classList.remove('d-none');
                toggleText.textContent = 'Passer à la recherche';
                if (dropdownSelect) dropdownSelect.focus();
            } else {
                dropdownWrapper.classList.add('d-none');
                searchWrapper.classList.remove('d-none');
                toggleText.textContent = 'Choisir dans la liste';
                if (searchInput) searchInput.focus();
            }
            if (window.lucide) window.lucide.createIcons();
        });
    }

    if (dropdownSelect) {
        dropdownSelect.addEventListener('change', function() {
            const opt = dropdownSelect.selectedOptions[0];
            if (opt && opt.value) {
                window.selectPatientInForm({
                    id: opt.value,
                    nom_complet: opt.dataset.nom || opt.textContent.trim(),
                    telephone: opt.dataset.tel || '',
                    groupe_sanguin: opt.dataset.groupe || '',
                    allergies: opt.dataset.allergies || '',
                    antecedents_personnels: opt.dataset.antPerso || '',
                    antecedents_familiaux: opt.dataset.antFam || ''
                });
            } else {
                resetPatientSelection();
            }
        });
    }

    function resetPatientSelection() {
        if (hiddenPatientId) hiddenPatientId.value = '';
        if (searchInput) searchInput.value = '';
        if (dropdownSelect) dropdownSelect.value = '';
        if (clearBtn) clearBtn.style.display = 'none';
        if (selectedPatientCard) selectedPatientCard.classList.add('d-none');
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', resetPatientSelection);
    }
    if (removePatientBtn) {
        removePatientBtn.addEventListener('click', resetPatientSelection);
    }
});

        // Le champ patient_id est cache : lui poser l'attribut required bloquerait
        // l'envoi sans message, le navigateur ne pouvant pas focaliser un champ
        // invisible. Le controle est donc fait ici, et le serveur revalide.
        (function () {
            var form = document.getElementById('consultationForm');
            var champCache = document.getElementById('patient_id');
            var champRecherche = document.getElementById('patient_search_input');
            if (!form || !champCache) return;

            form.addEventListener('submit', function (e) {
                if (champCache.value) return;

                e.preventDefault();

                if (champRecherche) {
                    champRecherche.classList.add('is-invalid');
                    champRecherche.focus();

                    var message = document.getElementById('patient_requis_message');
                    if (!message) {
                        message = document.createElement('div');
                        message.id = 'patient_requis_message';
                        message.className = 'text-danger small mt-1';
                        message.textContent = 'Sélectionnez un patient dans la liste avant de continuer.';
                        champRecherche.closest('.mb-3, .col-12, .col-md-6, div').appendChild(message);
                    }
                }
            });

            if (champRecherche) {
                champRecherche.addEventListener('input', function () {
                    champRecherche.classList.remove('is-invalid');
                    var message = document.getElementById('patient_requis_message');
                    if (message) message.remove();
                });
            }
        })();
</script>
@endsection

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
                        <div class="mb-3">
                            <label for="patient_id" class="form-label fw-bold">Patient <span class="text-danger">*</span></label>
                            @if($selectedPatient)
                                <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
                                <input type="text" class="form-control bg-light fw-bold" value="{{ $selectedPatient->nom }} {{ $selectedPatient->prenom }} (ID: #{{ $selectedPatient->id }})" readonly>
                            @else
                                <select name="patient_id" id="patient_id" class="form-select" required>
                                    <option value="">Sélectionnez un patient...</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nom }} {{ $p->prenom }} ({{ $p->telephone ?? 'Sans tél' }})
                                        </option>
                                    @endforeach
                                </select>
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
                                    <input type="text" name="tension_arterielle" class="form-control" placeholder="ex: 12/8" value="{{ old('tension_arterielle') }}">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Température</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="temperature" class="form-control" placeholder="ex: 37.2" value="{{ old('temperature') }}">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Poids</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="poids" id="poidsInput" class="form-control" placeholder="ex: 70" value="{{ old('poids') }}">
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Taille</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="taille" id="tailleInput" class="form-control" placeholder="ex: 175" value="{{ old('taille') }}">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Pouls</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="pouls" class="form-control" placeholder="ex: 75" value="{{ old('pouls') }}">
                                    <span class="input-group-text">bpm</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Glycémie</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="glycemie" class="form-control" placeholder="ex: 0.95" value="{{ old('glycemie') }}">
                                    <span class="input-group-text">g/L</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">SpO2 (Oxygène)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="saturation_oxygene" class="form-control" placeholder="ex: 98" value="{{ old('saturation_oxygene') }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Fréq. Respiratoire</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="frequence_respiratoire" class="form-control" placeholder="ex: 18" value="{{ old('frequence_respiratoire') }}">
                                    <span class="input-group-text">cpm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 3: Dossier Médical & Antécédents --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Dossier Patient & Antécédents</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Groupe Sanguin</label>
                            <select name="groupe_sanguin" class="form-select form-select-sm">
                                <option value="">Non déterminé</option>
                                @php
                                    $currentGroupe = $selectedPatient?->dossierMedical?->groupe_sanguin ?? old('groupe_sanguin');
                                @endphp
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gs)
                                    <option value="{{ $gs }}" {{ $currentGroupe == $gs ? 'selected' : '' }}>{{ $gs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-danger"><i data-lucide="alert-octagon" class="me-1"></i>Allergies Connues</label>
                            <input type="text" name="allergies" class="form-control form-control-sm" placeholder="ex: Pénicilline, Aspirine, Sulfamides" value="{{ old('allergies', $selectedPatient?->dossierMedical?->allergies) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Antécédents Personnels</label>
                            <textarea name="antecedents_personnels" rows="2" class="form-control form-control-sm" placeholder="ex: HTA, Diabète Type 2, Asthme, Drépanocytose">{{ old('antecedents_personnels', $selectedPatient?->dossierMedical?->antecedents_personnels) }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Antécédents Familiaux</label>
                            <textarea name="antecedents_familiaux" rows="2" class="form-control form-control-sm" placeholder="ex: HTA maternelle, Diabète paternel">{{ old('antecedents_familiaux', $selectedPatient?->dossierMedical?->antecedents_familiaux) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE: OBSERVATION CLINIQUE & ORDONNANCE --}}
            <div class="col-lg-7">
                {{-- Carte 4: Observation Clinique & Diagnostic --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="clipboard2-pulse" class="me-2"></i>Examen Clinique & Diagnostic</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="motif_consultation" class="form-label fw-bold">Motif de consultation <span class="text-danger">*</span></label>
                            <input type="text" name="motif_consultation" id="motif_consultation" class="form-control" placeholder="ex: Céphalées intenses, fièvre depuis 48h, toux sèche" value="{{ old('motif_consultation') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="histoire_maladie" class="form-label fw-bold">Histoire de la maladie / Symptômes</label>
                            <textarea name="histoire_maladie" id="histoire_maladie" rows="3" class="form-control" placeholder="Détail des signes fonctionnels, début d'apparition...">{{ old('histoire_maladie') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="examen_physique" class="form-label fw-bold">Examen Physique / Signes Cliniques</label>
                            <textarea name="examen_physique" id="examen_physique" rows="3" class="form-control" placeholder="Auscultation cardio-pulmonaire, palpation abdominale, réflexes...">{{ old('examen_physique') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnostic" class="form-label fw-bold text-primary">Diagnostic Médical / Hypothèse</label>
                            <input type="text" name="diagnostic" id="diagnostic" class="form-control form-control-lg border-primary fw-bold" placeholder="ex: Paludisme simple, Rhinopharyngite aiguë, Crise hypertensive" value="{{ old('diagnostic') }}">
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
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Prescription d'Ordonnance Médicale</h6>
                        <button type="button" class="btn btn-sm btn-light fw-bold" id="addPrescriptionBtn">
                            <i data-lucide="plus-circle" class="me-1"></i> Ajouter Médicament
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Renseignez les médicaments à prescrire au patient. Une ordonnance imprimable sera automatiquement générée.</p>

                        <div id="prescriptionsContainer">
                            {{-- Ligne 1 par défaut --}}
                            <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
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
});
</script>
@endsection

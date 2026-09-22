@extends('layout')

@section('title', 'Modifier la Consultation Médicale')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                Modifier Consultation N° <code>{{ $consultation->reference }}</code>
            </h1>
            <p class="text-muted mb-0">Mise à jour des constantes vitales, du diagnostic et des prescriptions.</p>
        </div>
        <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-outline-secondary">
            <i data-lucide="arrow-left" class="me-1"></i> Retour aux détails
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

    <form action="{{ route('consultations.update', $consultation) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- COLONNE GAUCHE: IDENTIFICATION & CONSTANTES --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="user" class="me-2"></i>Patient & Médecin</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Patient</label>
                            <input type="text" class="form-control bg-light fw-bold" value="{{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="medecin_id" class="form-label fw-bold">Médecin Traitant</label>
                            <select name="medecin_id" id="medecin_id" class="form-select">
                                <option value="">Sélectionnez le médecin...</option>
                                @foreach($medecins as $med)
                                    <option value="{{ $med->id }}" {{ (old('medecin_id', $consultation->medecin_id) == $med->id) ? 'selected' : '' }}>
                                        Dr {{ $med->nom }} {{ $med->prenom }} ({{ $med->specialite ?? 'Généraliste' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_consultation" class="form-label fw-bold">Date & Heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_consultation" id="date_consultation" class="form-control" value="{{ old('date_consultation', $consultation->date_consultation->format('Y-m-d\TH:i')) }}" required>
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
                                    <input type="text" name="tension_arterielle" class="form-control @error('tension_arterielle') is-invalid @enderror" placeholder="ex: 12/8" value="{{ old('tension_arterielle', $consultation->tension_arterielle) }}">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                                @error('tension_arterielle')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Température</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="temperature" class="form-control @error('temperature') is-invalid @enderror" placeholder="ex: 37.2" value="{{ old('temperature', $consultation->temperature) }}">
                                    <span class="input-group-text">°C</span>
                                </div>
                                @error('temperature')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Poids</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.1" name="poids" class="form-control @error('poids') is-invalid @enderror" placeholder="ex: 70" value="{{ old('poids', $consultation->poids) }}">
                                    <span class="input-group-text">kg</span>
                                </div>
                                @error('poids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Taille</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="taille" class="form-control @error('taille') is-invalid @enderror" placeholder="ex: 175 (ou 1.75)" value="{{ old('taille', $consultation->taille) }}">
                                    <span class="input-group-text">cm</span>
                                </div>
                                @error('taille')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Pouls</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="pouls" class="form-control @error('pouls') is-invalid @enderror" placeholder="ex: 75" value="{{ old('pouls', $consultation->pouls) }}">
                                    <span class="input-group-text">bpm</span>
                                </div>
                                @error('pouls')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-bold">Glycémie</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="glycemie" class="form-control @error('glycemie') is-invalid @enderror" placeholder="ex: 0.95 (ou 95)" value="{{ old('glycemie', $consultation->glycemie) }}">
                                    <span class="input-group-text">g/L</span>
                                </div>
                                @error('glycemie')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 3: Dossier Médical --}}
                @php $dos = $consultation->patient->dossierMedical; @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Dossier Patient & Antécédents</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Groupe Sanguin</label>
                            <select name="groupe_sanguin" class="form-select form-select-sm">
                                <option value="">Non déterminé</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gs)
                                    <option value="{{ $gs }}" {{ (old('groupe_sanguin', $dos?->groupe_sanguin) == $gs) ? 'selected' : '' }}>{{ $gs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-danger"><i data-lucide="alert-octagon" class="me-1"></i>Allergies</label>
                            <input type="text" name="allergies" class="form-control form-control-sm" value="{{ old('allergies', $dos?->allergies) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Antécédents Personnels</label>
                            <textarea name="antecedents_personnels" rows="2" class="form-control form-control-sm">{{ old('antecedents_personnels', $dos?->antecedents_personnels) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE: OBSERVATION CLINIQUE & ORDONNANCE --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="clipboard2-pulse" class="me-2"></i>Examen Clinique & Diagnostic</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="motif_consultation" class="form-label fw-bold">Motif de consultation <span class="text-danger">*</span></label>
                            <input type="text" name="motif_consultation" id="motif_consultation" class="form-control" value="{{ old('motif_consultation', $consultation->motif_consultation) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="histoire_maladie" class="form-label fw-bold">Histoire de la maladie / Symptômes</label>
                            <textarea name="histoire_maladie" id="histoire_maladie" rows="3" class="form-control">{{ old('histoire_maladie', $consultation->histoire_maladie) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="examen_physique" class="form-label fw-bold">Examen Physique / Signes Cliniques</label>
                            <textarea name="examen_physique" id="examen_physique" rows="3" class="form-control">{{ old('examen_physique', $consultation->examen_physique) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnostic" class="form-label fw-bold text-primary">Diagnostic Médical</label>
                            <input type="text" name="diagnostic" id="diagnostic" class="form-control form-control-lg border-primary fw-bold" value="{{ old('diagnostic', $consultation->diagnostic) }}">
                        </div>

                        <div class="mb-3">
                            <label for="conduite_a_tenir" class="form-label fw-bold">Conduite à tenir / Recommandations</label>
                            <textarea name="conduite_a_tenir" id="conduite_a_tenir" rows="2" class="form-control">{{ old('conduite_a_tenir', $consultation->conduite_a_tenir) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Prescription d'Ordonnance --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Prescription d'Ordonnance Médicale</h6>
                        <button type="button" class="btn btn-sm btn-light fw-bold" id="addPrescriptionBtn">
                            <i data-lucide="plus-circle" class="me-1"></i> Ajouter Médicament
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="prescriptionsContainer">
                            @php
                                $lignes = $consultation->ordonnance ? $consultation->ordonnance->lignes : [];
                            @endphp
                            @forelse($lignes as $idx => $ligne)
                                <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
                                    @if($idx > 0)
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-prescription-btn"></button>
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold">Médicament</label>
                                            <input type="text" name="prescriptions[{{ $idx }}][medicament]" class="form-control form-control-sm" value="{{ $ligne->medicament }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Forme & Dosage</label>
                                            <input type="text" name="prescriptions[{{ $idx }}][dosage]" class="form-control form-control-sm" value="{{ $ligne->dosage }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Durée</label>
                                            <input type="text" name="prescriptions[{{ $idx }}][duree]" class="form-control form-control-sm" value="{{ $ligne->duree }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">Posologie</label>
                                            <input type="text" name="prescriptions[{{ $idx }}][posologie]" class="form-control form-control-sm" value="{{ $ligne->posologie }}">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold">Médicament</label>
                                            <input type="text" name="prescriptions[0][medicament]" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Forme & Dosage</label>
                                            <input type="text" name="prescriptions[0][dosage]" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Durée</label>
                                            <input type="text" name="prescriptions[0][duree]" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">Posologie</label>
                                            <input type="text" name="prescriptions[0][posologie]" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Instructions Générales</label>
                            <input type="text" name="instructions_generales" class="form-control form-control-sm" value="{{ old('instructions_generales', $consultation->ordonnance?->instructions_generales) }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mb-5">
                    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-secondary px-4">Annuler</a>
                    <button type="submit" class="btn btn-warning btn-lg px-4 shadow-sm fw-bold">
                        <i data-lucide="check-circle" class="me-1"></i> Mettre à jour la Consultation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = {{ max(count($consultation->ordonnance?->lignes ?? []), 1) }};
    const addBtn = document.getElementById('addPrescriptionBtn');
    const container = document.getElementById('prescriptionsContainer');

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            const rowHtml = `
                <div class="prescription-row border rounded p-2 mb-3 bg-light position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-prescription-btn"></button>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Médicament</label>
                            <input type="text" name="prescriptions[${rowIndex}][medicament]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Forme & Dosage</label>
                            <input type="text" name="prescriptions[${rowIndex}][dosage]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Durée</label>
                            <input type="text" name="prescriptions[${rowIndex}][duree]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Posologie</label>
                            <input type="text" name="prescriptions[${rowIndex}][posologie]" class="form-control form-control-sm">
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

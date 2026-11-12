@extends('layout')

@section('title', 'Détails de la Consultation Médicale')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('consultations.index') }}" class="btn btn-light border bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour aux consultations">
                <i data-lucide="arrow-left" style="width: 1.15rem; height: 1.15rem;"></i>
            </a>
            <div>
                <h1 class="h3 text-dark fw-bold mb-0">Consultation N° <span class="font-mono text-teal">{{ $consultation->reference }}</span></h1>
                <small class="text-muted">Observation clinique et suivi médical &bull; Date : <span class="font-mono">{{ $consultation->date_consultation->format('d/m/Y à H:i') }}</span></small>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($consultation->ordonnance)
                <a href="{{ route('consultations.print-ordonnance', $consultation) }}" target="_blank" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-1">
                    <i data-lucide="printer" style="width: 1rem; height: 1rem;"></i>
                    <span>Imprimer Ordonnance</span>
                </a>
            @endif
            <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-1">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
                <span>Modifier</span>
            </a>
        </div>
    </div>

    @if(session('alert'))
        <div class="alert alert-success bg-success-subtle border-success-subtle rounded-3 p-3 mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem;" class="text-success"></i>
            <span class="text-success fw-semibold">{{ session('alert') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- COLONNE GAUCHE : PATIENT & CONSTANTES --}}
        <div class="col-lg-5">
            {{-- Fiche Patient --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="user-check" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Patient & Médecin</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted small d-block">Patient :</span>
                        <a href="{{ route('patients.show', $consultation->patient) }}" class="fs-5 fw-bold text-dark text-decoration-none">
                            {{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}
                        </a>
                        <div class="small text-muted mt-1">
                            Sexe: <strong>{{ $consultation->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</strong> |
                            Tél: <strong>{{ $consultation->patient->telephone ?? 'Non renseigné' }}</strong>
                        </div>
                        @if($consultation->patient->statut === 'assure')
                            <div class="badge bg-success mt-2">
                                <i data-lucide="shield-check" class="me-1"></i> Assuré : {{ $consultation->patient->assurance->nom ?? 'Conventionnée' }} ({{ number_format($consultation->patient->taux_couverture ?? 80, 0) }}%)
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-3">
                        <span class="text-muted small d-block">Médecin traitant :</span>
                        @if($consultation->medecin)
                            <strong class="fs-6 text-dark">Dr {{ $consultation->medecin->nom }} {{ $consultation->medecin->prenom }}</strong>
                            <div class="small text-primary">{{ $consultation->medecin->specialite ?? 'Généraliste' }}</div>
                        @else
                            <span class="text-muted fst-italic">Non assigné</span>
                        @endif
                    </div>

                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Date consultation :</span>
                            <strong>{{ $consultation->date_consultation->format('d/m/Y à H:i') }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Ticket caisse :</span>
                            @if($consultation->ticket)
                                <a href="{{ route('tickets.show', $consultation->ticket) }}" class="fw-bold text-decoration-none">
                                    {{ $consultation->ticket->reference }}
                                </a>
                            @else
                                <span class="text-muted">Aucun</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Constantes Vitales --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0 fw-bold"><i data-lucide="activity" class="me-2"></i>Constantes Vitales Relevées</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Tension Artérielle</small>
                                <strong class="fs-5 text-dark">{{ $consultation->tension_arterielle ?? '--' }}</strong> <small class="text-muted">mmHg</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Température</small>
                                <strong class="fs-5 text-dark">{{ $consultation->temperature ?? '--' }}</strong> <small class="text-muted">°C</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Poids</small>
                                <strong class="fs-5 text-dark">{{ $consultation->poids ?? '--' }}</strong> <small class="text-muted">kg</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Taille</small>
                                <strong class="fs-5 text-dark">{{ $consultation->taille ?? '--' }}</strong> <small class="text-muted">cm</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Pouls</small>
                                <strong class="fs-5 text-dark">{{ $consultation->pouls ?? '--' }}</strong> <small class="text-muted">bpm</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light text-center">
                                <small class="text-muted d-block">Glycémie</small>
                                <strong class="fs-5 text-dark">{{ $consultation->glycemie ?? '--' }}</strong> <small class="text-muted">g/L</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Antécédents & Dossier --}}
            @if($consultation->patient && $consultation->patient->dossierMedical)
                @php $dos = $consultation->patient->dossierMedical; @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2"></i>Dossier Médical Permanent</h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-1"><strong>Groupe Sanguin :</strong> <span class="badge bg-danger">{{ $dos->groupe_sanguin ?? 'Non renseigné' }}</span></p>
                        <p class="mb-1 text-danger"><strong>Allergies :</strong> {{ $dos->allergies ?? 'Aucune connue' }}</p>
                        <p class="mb-1"><strong>Antécédents Personnels :</strong> {{ $dos->antecedents_personnels ?? 'Néant' }}</p>
                        <p class="mb-0"><strong>Antécédents Familiaux :</strong> {{ $dos->antecedents_familiaux ?? 'Néant' }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- COLONNE DROITE : OBSERVATION CLINIQUE & ORDONNANCE --}}
        <div class="col-lg-7">
            {{-- Examen Clinique & Diagnostic --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="activity" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Observation Clinique & Diagnostic</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <span class="text-muted small d-block fw-bold text-uppercase mb-1">Motif de Consultation</span>
                        <p class="fs-6 fw-semibold text-dark mb-0">{{ $consultation->motif_consultation }}</p>
                    </div>

                    @if($consultation->histoire_maladie)
                        <div class="mb-4">
                            <span class="text-muted small d-block fw-bold text-uppercase mb-1">Histoire de la Maladie / Symptômes</span>
                            <div class="p-3 bg-light rounded-3 text-dark">{{ nl2br(e($consultation->histoire_maladie)) }}</div>
                        </div>
                    @endif

                    @if($consultation->examen_physique)
                        <div class="mb-4">
                            <span class="text-muted small d-block fw-bold text-uppercase mb-1">Examen Physique / Signes Cliniques</span>
                            <div class="p-3 bg-light rounded-3 text-dark">{{ nl2br(e($consultation->examen_physique)) }}</div>
                        </div>
                    @endif

                    <div class="mb-4 p-3 rounded-3" style="background: #e2f1ef; border-left: 4px solid #0f766e;">
                        <span class="text-teal small d-block fw-bold text-uppercase mb-1 d-flex align-items-center gap-1">
                            <i data-lucide="check-circle" style="width: 0.95rem; height: 0.95rem;"></i>
                            <span>Diagnostic Retenu</span>
                        </span>
                        <h4 class="fw-bold text-teal mb-0">{{ $consultation->diagnostic ?? 'Diagnostic en cours d\'exploration' }}</h4>
                    </div>

                    @if($consultation->conduite_a_tenir)
                        <div class="mb-2">
                            <span class="text-muted small d-block fw-bold text-uppercase mb-1">Conduite à Tenir / Recommandations</span>
                            <div class="p-3 bg-light rounded-3 text-dark">{{ nl2br(e($consultation->conduite_a_tenir)) }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ordonnance Médicale Associée --}}
            @if($consultation->ordonnance)
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                            <span>Ordonnance Médicale Prescrite</span>
                        </h5>
                        <a href="{{ route('consultations.print-ordonnance', $consultation) }}" target="_blank" class="btn btn-sm btn-teal fw-semibold d-flex align-items-center gap-1">
                            <i data-lucide="printer" style="width: 0.85rem; height: 0.85rem;"></i>
                            <span>Imprimer</span>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span>Réf : <strong>{{ $consultation->ordonnance->reference }}</strong></span>
                            <span>Date : <strong>{{ $consultation->ordonnance->date_ordonnance->format('d/m/Y') }}</strong></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Médicament (DCI)</th>
                                        <th>Dosage / Forme</th>
                                        <th>Posologie & Instructions</th>
                                        <th>Durée</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($consultation->ordonnance->lignes as $idx => $ligne)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                            <td class="fw-bold text-dark">{{ $ligne->medicament }}</td>
                                            <td>{{ $ligne->dosage }} {{ $ligne->forme }}</td>
                                            <td>
                                                <strong>{{ $ligne->posologie }}</strong>
                                                @if($ligne->instructions)
                                                    <small class="d-block text-muted">{{ $ligne->instructions }}</small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $ligne->duree ?? '-' }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucun médicament spécifié.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($consultation->ordonnance->instructions_generales)
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <strong>Instructions générales :</strong> {{ $consultation->ordonnance->instructions_generales }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

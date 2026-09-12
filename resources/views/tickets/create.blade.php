@extends('layout')

@section('title', 'Émettre un Nouveau Ticket de Facturation')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec navigation retour --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-plus-circle-fill me-2"></i>Émettre un Ticket de Facturation
            </h1>
            <p class="text-muted mb-0">Création d'un ticket de consultation avec autocomplétion patient et validité 7 jours.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la Liste
        </a>
    </div>

    <form action="{{ route('tickets.store') }}" method="POST" class="card shadow-sm border-0 rounded-3">
        @csrf

        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Formulaire d'Émission de Ticket</h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- 1. Référence automatique (Lecture seule) --}}
                <div class="col-md-3">
                    <label for="reference" class="form-label fw-bold text-dark">
                        <i class="bi bi-barcode text-primary me-1"></i> Référence Ticket
                    </label>
                    <input type="text" name="reference" id="reference" class="form-control bg-light fw-bold font-monospace text-primary" value="{{ old('reference', $defaultReference) }}" readonly>
                    <small class="text-muted">Code automatique.</small>
                </div>

                {{-- 2. Recherche Patient avec Autocomplétion (Nom ou N° Téléphone) --}}
                @php
                    $initPatientName = isset($selectedPatient) ? $selectedPatient->prenom . ' ' . $selectedPatient->nom : '';
                    $initPatientId = isset($selectedPatient) ? $selectedPatient->id : request()->get('patient_id');
                    $initCarte = isset($selectedPatient) && $selectedPatient->cartesAssurance ? $selectedPatient->cartesAssurance->where('statut', true)->first() : null;
                    $initAssuranceId = $initCarte ? $initCarte->assurance_id : '';
                    $initTaux = $initCarte ? (float)$initCarte->taux_couverture : 0;
                @endphp
                <div class="col-md-4 position-relative">
                    <label for="patient_search_input" class="form-label fw-bold text-dark">
                        <i class="bi bi-search text-primary me-1"></i> Rechercher Patient <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="patient_search_input" class="form-control @error('patient_id') is-invalid @enderror" placeholder="Nom, prénom ou N° de tél..." data-url="{{ route('patients.search') }}" value="{{ old('patient_search_name', $initPatientName) }}" autocomplete="off" required>
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $initPatientId) }}" required>
                    
                    {{-- Liste d'autocomplétion dynamique --}}
                    <div id="patient_results_list" class="list-group position-absolute w-100 shadow rounded-3 mt-1" style="display: none; z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                    
                    @error('patient_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Autocomplétion dynamique.</small>
                </div>

                {{-- 3. Sélecteur Organisme d'Assurance --}}
                <div class="col-md-3">
                    <label for="assurance_id" class="form-label fw-bold text-dark">
                        <i class="bi bi-shield-check text-success me-1"></i> Organisme Assurance
                    </label>
                    <select name="assurance_id" id="assurance_id" class="form-select fw-semibold @error('assurance_id') is-invalid @enderror">
                        <option value="">-- Aucune (100% Patient) --</option>
                        @foreach($assurances as $assurance)
                            <option value="{{ $assurance->id }}" {{ old('assurance_id', $initAssuranceId) == $assurance->id ? 'selected' : '' }}>
                                {{ $assurance->nom }} ({{ $assurance->code ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('assurance_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Sélectionnable ou auto-détecté.</small>
                </div>

                {{-- 4. Agent Caissier Authentifié (Lecture seule) --}}
                <div class="col-md-2">
                    <label class="form-label fw-bold text-dark">
                        <i class="bi bi-person-badge text-primary me-1"></i> Agent / Caissier
                    </label>
                    <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ $currentUser->name ?? 'Admin' }}" readonly>
                    <input type="hidden" name="user_id" value="{{ $currentUser->id ?? 1 }}">
                </div>

                {{-- 5. Choix du Service Médical (ex: Consultation Générale) --}}
                <div class="col-md-6">
                    <label for="service_id" class="form-label fw-bold text-dark">
                        <i class="bi bi-hospital text-primary me-1"></i> Service Médical
                    </label>
                    <select name="service_id" id="service_id" class="form-select fw-semibold @error('service_id') is-invalid @enderror">
                        <option value="">-- Sélectionner un Service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }} {{ $service->code ? '('.$service->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Ex: Consultation générale, Pédiatrie...</small>
                </div>

                {{-- 6. Choix du Médecin Traitant & Spécialité --}}
                <div class="col-md-6">
                    <label for="medecin_id" class="form-label fw-bold text-dark">
                        <i class="bi bi-person-heart text-primary me-1"></i> Médecin Traitant & Spécialité
                    </label>
                    <select name="medecin_id" id="medecin_id" class="form-select fw-semibold @error('medecin_id') is-invalid @enderror">
                        <option value="">-- Sélectionner un Médecin --</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                Dr {{ $medecin->nom }} {{ $medecin->prenom }} {{ $medecin->specialite ? ' (Spécialité : '.$medecin->specialite.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('medecin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Médecin traitant et sa spécialité.</small>
                </div>

                {{-- 4. Dates : Date Ticket (Maintenant) & Date Expiration (Valable 7 Jours automatique) --}}
                <div class="col-md-6">
                    <label for="date_ticket" class="form-label fw-bold text-dark">
                        <i class="bi bi-calendar-event text-primary me-1"></i> Date d'Émission <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="date_ticket" id="date_ticket" class="form-control @error('date_ticket') is-invalid @enderror" value="{{ old('date_ticket', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required>
                    @error('date_ticket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_expiration" class="form-label fw-bold text-dark">
                        <i class="bi bi-calendar-check text-success me-1"></i> Date d'Expiration (Automatique : 7 Jours)
                    </label>
                    <input type="datetime-local" name="date_expiration" id="date_expiration" class="form-control @error('date_expiration') is-invalid @enderror" value="{{ old('date_expiration', \Carbon\Carbon::now()->addDays(7)->format('Y-m-d\TH:i')) }}">
                    <small class="text-success font-weight-bold">Valable 7 jours par défaut selon le règlement.</small>
                    @error('date_expiration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr class="my-3 text-muted">

                {{-- 5. Saisie des Montants & Taux de Prise en Charge en Pourcentage (%) --}}
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-percent text-primary me-2"></i>Tarification & Taux de Prise en Charge Assurance (%)</h5>

                {{-- Montant Brut --}}
                <div class="col-md-4">
                    <label for="montant_total" class="form-label fw-bold text-dark">
                        Montant Total Brut (FBU) <span class="text-danger">*</span>
                    </label>
                    <input type="number" step="1" min="0" name="montant_total" id="montant_total" class="form-control form-control-lg fw-bold @error('montant_total') is-invalid @enderror" value="{{ old('montant_total', 0) }}" required placeholder="0">
                    @error('montant_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Taux Prise en Charge Assurance en Pourcentage (%) --}}
                <div class="col-md-4">
                    <label for="taux_assurance" class="form-label fw-bold text-dark">
                        Part Prise en Charge Assurance (%) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="number" step="1" min="0" max="100" name="taux_assurance" id="taux_assurance" class="form-control text-success fw-bold @error('taux_assurance') is-invalid @enderror" value="{{ old('taux_assurance', 0) }}" placeholder="0">
                        <span class="input-group-text bg-success-subtle text-success fw-bold">%</span>
                    </div>
                    {{-- Boutons d'accès rapide aux taux courants --}}
                    <div class="d-flex gap-1 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-taux" data-taux="0">0%</button>
                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 btn-taux" data-taux="70">70%</button>
                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 btn-taux" data-taux="80">80%</button>
                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 btn-taux" data-taux="90">90%</button>
                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 btn-taux" data-taux="100">100%</button>
                    </div>
                    @error('taux_assurance')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                {{-- Net à Payer par le Patient (Calculé dynamiquement) --}}
                <div class="col-md-4">
                    <label for="montant_patient" class="form-label fw-bold text-dark">
                        Net à Payer par le Patient (FBU) <span class="text-danger">*</span>
                    </label>
                    <input type="number" step="1" min="0" name="montant_patient" id="montant_patient" class="form-control form-control-lg text-primary fw-bold @error('montant_patient') is-invalid @enderror" value="{{ old('montant_patient', 0) }}" required readonly placeholder="0">
                    <input type="hidden" name="montant_assurance" id="montant_assurance" value="{{ old('montant_assurance', 0) }}">
                    <small class="text-muted">Calculé: Montant Brut - (Brut × Taux %)</small>
                    @error('montant_patient')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Montant Encaissé au Guichet --}}
                <div class="col-md-6">
                    <label for="montant_paye" class="form-label fw-bold text-dark">
                        Montant Encaissé au Guichet (Optionnel)
                    </label>
                    <input type="number" step="1" min="0" name="montant_paye" id="montant_paye" class="form-control fw-bold text-success @error('montant_paye') is-invalid @enderror" value="{{ old('montant_paye', 0) }}" placeholder="0">
                    @error('montant_paye')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Statut --}}
                <div class="col-md-6">
                    <label for="statut" class="form-label fw-bold text-dark">
                        Statut du Ticket <span class="text-danger">*</span>
                    </label>
                    <select name="statut" id="statut" class="form-select fw-bold @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', 'impaye') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label fw-bold text-dark">Observations / Motifs de consultation</label>
                    <textarea name="description" id="description" class="form-control" rows="2" placeholder="Précisez le type de consultation ou observations particulières...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-4 text-end">
            <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow-sm">
                <i class="bi bi-printer-fill me-2"></i> Enregistrer & Imprimer Ticket ➔
            </button>
        </div>
    </form>
</div>
@endsection

@stack('scripts')
<script>
    window.defaultTicketReference = "{{ $defaultReference }}";

    // Calcul automatique dynamique du Taux Assurance (%) et Net Patient
    document.addEventListener('DOMContentLoaded', function() {
        const inputTotal = document.getElementById('montant_total');
        const inputTaux = document.getElementById('taux_assurance');
        const inputAssuranceHidden = document.getElementById('montant_assurance');
        const inputPatient = document.getElementById('montant_patient');
        const btnTauxList = document.querySelectorAll('.btn-taux');

        function recalculerVentilation() {
            const total = parseFloat(inputTotal.value) || 0;
            const taux = Math.max(0, Math.min(100, parseFloat(inputTaux.value) || 0));

            const partAssurance = Math.round((total * taux) / 100);
            const netPatient = Math.max(0, total - partAssurance);

            inputAssuranceHidden.value = partAssurance;
            inputPatient.value = netPatient;
        }

        if (inputTotal && inputTaux && inputPatient) {
            inputTotal.addEventListener('input', recalculerVentilation);
            inputTaux.addEventListener('input', recalculerVentilation);

            // Clic sur les boutons d'accès rapide aux pourcentages (0%, 70%, 80%, 90%, 100%)
            btnTauxList.forEach(btn => {
                btn.addEventListener('click', function() {
                    inputTaux.value = this.dataset.taux;
                    recalculerVentilation();
                });
            });
        }
    });
</script>

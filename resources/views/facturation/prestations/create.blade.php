@extends('layout')

@section('title', 'Enregistrement d\'une Prestation')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-1 text-primary"><i data-lucide="file-medical" class="me-2"></i>Nouvelle Prestation Médicale</h1>
                <p class="text-muted small mb-0">Saisie simplifiée : les calculs de facturation et de partage sont gérés automatiquement par le système.</p>
            </div>
            <a href="{{ route('prestations.index') }}" class="btn btn-outline-secondary btn-sm"><i data-lucide="arrow-left" class="me-1"></i>Retour à la liste</a>
        </div>

        <form action="{{ route('prestations.store') }}" method="post" id="prestationForm">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger mb-3 shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            {{-- Champ de sélection du patient --}}
                            <div class="mb-3 position-relative">
                                <label for="patient_search_input" class="form-label fw-bold">Patient <span class="text-danger">*</span> : </label>
                                <input type="text"
                                       id="patient_search_input"
                                       class="form-control form-control-lg @error('patient_id') is-invalid @enderror"
                                       placeholder="Tapez le nom ou le prénom du patient..."
                                       data-url="{{ route('patients.search') }}"
                                       value="{{ old('patient_name', $selectedPatient ? $selectedPatient->prenom . ' ' . $selectedPatient->nom : '') }}"
                                       autocomplete="off" required>
                                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $selectedPatient->id ?? '') }}">
                                <div id="patient_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>
                                @error('patient_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Champ de sélection du service --}}
                            <div class="mb-3 position-relative">
                                <label for="service_search_input" class="form-label fw-bold">Service Médical <span class="text-danger">*</span> : </label>
                                <input type="text"
                                       id="service_search_input"
                                       class="form-control form-control-lg @error('service_id') is-invalid @enderror"
                                       placeholder="Tapez le nom du service (ex: Consultation générale)..."
                                       data-url="{{ route('services.search') }}"
                                       value="{{ old('service_name', $selectedService ? $selectedService->nom . ' (' . $selectedService->code . ')' : '') }}"
                                       autocomplete="off" required>
                                <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id', $selectedService->id ?? '') }}">
                                <div id="service_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>
                                @error('service_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Choix du médecin --}}
                            <div class="mb-3">
                                <label for="medecin_id" class="form-label fw-bold">Médecin Traitant : </label>
                                <select name="medecin_id" id="medecin_id" class="form-select @error('medecin_id') is-invalid @enderror">
                                    <option value="">-- Aucun / Non attribué --</option>
                                    @foreach($medecins as $medecin)
                                        <option value="{{ $medecin->id }}" {{ (string)old('medecin_id') === (string)$medecin->id ? 'selected' : '' }}>
                                            Dr. {{ $medecin->prenom }} {{ $medecin->nom }} {{ $medecin->specialite ? '(' . $medecin->specialite . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('medecin_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_prestation" class="form-label fw-bold">Date & Heure : </label>
                                    <input type="datetime-local" name="date_prestation" id="date_prestation" class="form-control" value="{{ old('date_prestation', now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label fw-bold">Observations / Motif : </label>
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Observations optionnelles..." value="{{ old('description') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panneau latéral de calcul automatique dynamique --}}
                <div class="col-md-4">
                    <div class="card border-primary border-2 shadow-sm bg-light mb-3">
                        <div class="card-header bg-primary text-white font-weight-bold py-3">
                            <h5 class="card-title mb-0 h6 text-white"><i data-lucide="calculator" class="me-2"></i>Résumé Automatique des Calculs</h5>
                        </div>
                        <div class="card-body">
                            <div id="previewPlaceholder" class="text-center py-4 text-muted">
                                <i data-lucide="info" class="display-6 mb-2 d-block text-secondary"></i>
                                Sélectionnez un <strong>Patient</strong> et un <strong>Service</strong> pour afficher le calcul en temps réel.
                            </div>

                            <div id="previewCard" style="display: none;">
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Tarif Actif Brut :</span>
                                    <strong id="lblTarifBrut" class="fs-6">0 FCFA</strong>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Taux Couverture Assurance :</span>
                                    <span id="lblTauxAssurance" class="badge bg-info text-dark fs-6">0 %</span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Part Assurance :</span>
                                    <strong id="lblPartAssurance" class="text-primary fs-6">0 FCFA</strong>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom bg-white p-2 rounded my-2 shadow-sm">
                                    <span class="fw-bold text-dark">Part Patient à Payer :</span>
                                    <strong id="lblPartPatient" class="text-success fs-5">0 FCFA</strong>
                                </div>

                                <hr class="my-2">

                                <div class="d-flex justify-content-between py-1">
                                    <span class="small text-muted">Part Médecin (<span id="lblPctMedecin">50</span>%) :</span>
                                    <span id="lblPartMedecin" class="small fw-bold">0 FCFA</span>
                                </div>

                                <div class="d-flex justify-content-between py-1">
                                    <span class="small text-muted">Part Clinique (<span id="lblPctClinique">50</span>%) :</span>
                                    <span id="lblPartClinique" class="small fw-bold">0 FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-center py-3">
                            <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm"><i data-lucide="check-circle" class="me-2"></i>Valider & Générer Ticket</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Script JavaScript Vanilla pour la mise à jour dynamique des calculs serveur --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const patientIdInput = document.getElementById('patient_id');
            const serviceIdInput = document.getElementById('service_id');
            const medecinSelect = document.getElementById('medecin_id');
            const dateInput = document.getElementById('date_prestation');

            const previewPlaceholder = document.getElementById('previewPlaceholder');
            const previewCard = document.getElementById('previewCard');

            const previewUrl = "{{ route('prestations.preview') }}";
            const csrfToken = "{{ csrf_token() }}";

            function triggerPreview() {
                const patientId = patientIdInput.value;
                const serviceId = serviceIdInput.value;

                if (!patientId || !serviceId) {
                    previewPlaceholder.style.display = 'block';
                    previewCard.style.display = 'none';
                    return;
                }

                fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        service_id: serviceId,
                        medecin_id: medecinSelect.value || null,
                        date_prestation: dateInput.value || null
                    })
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        document.getElementById('lblTarifBrut').textContent = new Intl.NumberFormat('fr-FR').format(d.tarif_brut) + ' FCFA';
                        document.getElementById('lblTauxAssurance').textContent = d.taux_couverture + ' %';
                        document.getElementById('lblPartAssurance').textContent = new Intl.NumberFormat('fr-FR').format(d.montant_assurance) + ' FCFA';
                        document.getElementById('lblPartPatient').textContent = new Intl.NumberFormat('fr-FR').format(d.montant_patient) + ' FCFA';

                        document.getElementById('lblPctMedecin').textContent = d.pourcentage_medecin;
                        document.getElementById('lblPartMedecin').textContent = new Intl.NumberFormat('fr-FR').format(d.part_medecin) + ' FCFA';

                        document.getElementById('lblPctClinique').textContent = d.pourcentage_clinique;
                        document.getElementById('lblPartClinique').textContent = new Intl.NumberFormat('fr-FR').format(d.part_clinique) + ' FCFA';

                        previewPlaceholder.style.display = 'none';
                        previewCard.style.display = 'block';
                    }
                })
                .catch(err => console.error("Erreur calcul preview:", err));
            }

            // Écouteurs d'événements sur les sélecteurs
            patientIdInput.addEventListener('change', triggerPreview);
            serviceIdInput.addEventListener('change', triggerPreview);
            medecinSelect.addEventListener('change', triggerPreview);
            dateInput.addEventListener('change', triggerPreview);

            // Observer pour les changements de valeur sur inputs cachés (autocomplétion)
            const observer = new MutationObserver(triggerPreview);
            observer.observe(patientIdInput, { attributes: true, attributeFilter: ['value'] });
            observer.observe(serviceIdInput, { attributes: true, attributeFilter: ['value'] });

            // Lancement initial si prérempli
            if (patientIdInput.value && serviceIdInput.value) {
                triggerPreview();
            }
        });
    </script>
@endsection
{{-- blade cache refresh --}}

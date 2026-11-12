@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Modification de la Prestation')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de modification avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier la Prestation #{{ $prestation->id }}</h1>
            {{-- Bouton pour revenir à la liste des prestations --}}
            <a href="{{ route('prestations.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de mise à jour d'une prestation --}}
        <form action="{{ route('prestations.update', $prestation) }}" method="post">
            {{-- Jeton CSRF pour sécuriser la soumission du formulaire --}}
            @csrf
            {{-- Directive Blade simulant la méthode HTTP PUT pour la mise à jour --}}
            @method('PUT')

            {{-- Affichage du bloc des erreurs de validation --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ de recherche autocomplété pour le patient par nom ou prénom --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche du patient --}}
                <label for="patient_search_input" class="form-label">Patient (Recherche par nom ou prénom) : </label>

                {{-- Champ texte pré-rempli avec le prénom et nom du patient sélectionné --}}
                <input type="text"
                       id="patient_search_input"
                       class="form-control @error('patient_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le prénom du patient..."
                       data-url="{{ route('patients.search') }}"
                       value="{{ old('patient_name', $selectedPatient ? $selectedPatient->prenom . ' ' . $selectedPatient->nom : '') }}"
                       autocomplete="off">

                {{-- Champ caché stockant l'ID du patient sélectionné --}}
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $prestation->patient_id) }}">

                {{-- Conteneur dynamique des suggestions de recherche de patients --}}
                <div id="patient_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Message d'erreur de validation pour le patient --}}
                @error('patient_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ de recherche autocomplété pour le service médical par nom ou code --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche du service médical --}}
                <label for="service_search_input" class="form-label">Service Médical (Recherche par nom ou code) : </label>

                {{-- Champ texte pré-rempli avec le nom et le code du service --}}
                <input type="text"
                       id="service_search_input"
                       class="form-control @error('service_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le code du service..."
                       data-url="{{ route('services.search') }}"
                       value="{{ old('service_name', $selectedService ? $selectedService->nom . ' (' . $selectedService->code . ')' : '') }}"
                       autocomplete="off">

                {{-- Champ caché stockant l'ID du service sélectionné --}}
                <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id', $prestation->service_id) }}">

                {{-- Conteneur dynamique des suggestions pour le service --}}
                <div id="service_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Message d'erreur de validation pour le service --}}
                @error('service_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Liste déroulante pour la modification de l'acte applicable --}}
            <div class="mb-3">
                <label for="acte_id" class="form-label">Acte / Nomenclature Applicable : </label>
                <select name="acte_id" id="acte_id" class="form-select @error('acte_id') is-invalid @enderror">
                    <option value="">-- Sélectionner un acte (Optionnel) --</option>
                    @foreach($actes as $acte)
                        <option value="{{ $acte->id }}" {{ (string)old('acte_id', $prestation->acte_id) === (string)$acte->id ? 'selected' : '' }}>
                            [{{ $acte->code }}] {{ $acte->nom }} ({{ $acte->service->nom ?? 'Général' }}) - {{ number_format($acte->tarif_base, 0, ',', ' ') }} FCFA
                        </option>
                    @endforeach
                </select>
                @error('acte_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Liste déroulante pour la modification du médecin traitant --}}
            <div class="mb-3">
                <label for="medecin_id" class="form-label">Médecin Traitant (Optionnel) : </label>
                <select name="medecin_id" id="medecin_id" class="form-select @error('medecin_id') is-invalid @enderror">
                    <option value="">-- Aucun / Non attribué --</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" {{ (string)old('medecin_id', $prestation->medecin_id) === (string)$medecin->id ? 'selected' : '' }}>
                            Dr. {{ $medecin->prenom }} {{ $medecin->nom }} {{ $medecin->specialite ? '(' . $medecin->specialite . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('medecin_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Champ pour modifier le type de prestation --}}
            <x-form.input type="text" name="type" label="Type / Catégorie de prestation : " value="{{ old('type', $prestation->type) }}" />

            {{-- Champ pour modifier le montant de la prestation --}}
            <x-form.input type="number" name="montant" label="Montant de la prestation (FCFA) : " value="{{ old('montant', $prestation->montant) }}" />

            {{-- Champ pour modifier la date et heure de réalisation --}}
            <x-form.input type="datetime-local" name="date_prestation" label="Date et Heure de réalisation : " value="{{ old('date_prestation', $prestation->date_prestation ? $prestation->date_prestation->format('Y-m-d\TH:i') : '') }}" />

            {{-- Champ pour modifier la description ou observations --}}
            <x-form.input type="text" name="description" label="Description / Compte-rendu : " value="{{ old('description', $prestation->description) }}" />

            {{-- Sélection du statut de la prestation avec valeur courante pré-sélectionnée --}}
            <x-form.input type="select" name="statut" label="Statut de la prestation : " :options="$statuts" value="{{ old('statut', $prestation->statut ? '1' : '0') }}" />

            {{-- Bouton pour valider et enregistrer les modifications --}}
            <button type="submit" class="btn btn-warning">Mettre à jour la prestation</button>
        </form>
    </section>
@endsection

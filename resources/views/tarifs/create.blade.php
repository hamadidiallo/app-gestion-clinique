@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Création d\'un Tarif')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de création avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Création d'un Nouveau Tarif</h1>
            {{-- Bouton pour revenir à la liste des tarifs --}}
            <a href="{{ route('tarifs.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire d'enregistrement d'un nouveau tarif --}}
        <form action="{{ route('tarifs.store') }}" method="post">
            {{-- Jeton CSRF obligatoire pour sécuriser la requête POST --}}
            @csrf

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

            {{-- Champ de recherche autocomplété pour le service médical par nom ou code --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche autocomplété --}}
                <label for="service_search_input" class="form-label">Service Médical (Recherche par nom ou code) : </label>

                {{-- Champ de saisie texte déclenchant la recherche AJAX d'autocomplétion --}}
                <input type="text"
                       id="service_search_input"
                       class="form-control @error('service_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le code du service..."
                       data-url="{{ route('services.search') }}"
                       value="{{ old('service_name', $selectedService ? $selectedService->nom . ' (' . $selectedService->code . ')' : '') }}"
                       autocomplete="off">

                {{-- Champ caché stockant l'ID du service sélectionné pour la soumission du formulaire --}}
                <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id', $selectedService->id ?? '') }}">

                {{-- Conteneur dynamique des suggestions de services médicaux --}}
                <div id="service_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Affichage du message d'erreur si la validation du service échoue --}}
                @error('service_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Saisie du tarif normal obligatoire --}}
            <x-form.input type="number" name="tarif_normal" label="Tarif Normal (FCFA) : " value="{{ old('tarif_normal') }}" />

            {{-- Saisie du tarif AMO facultatif --}}
            <x-form.input type="number" name="tarif_amo" label="Tarif AMO (FCFA) : " value="{{ old('tarif_amo') }}" />

            {{-- Saisie du tarif spécifique facultatif --}}
            <x-form.input type="number" name="tarif_specifique" label="Tarif Spécifique (FCFA) : " value="{{ old('tarif_specifique') }}" />

            {{-- Saisie de la date de début d'application du tarif --}}
            <x-form.input type="date" name="date_debut" label="Date de début d'effet : " value="{{ old('date_debut') }}" />

            {{-- Saisie de la date de fin d'application du tarif --}}
            <x-form.input type="date" name="date_fin" label="Date de fin d'effet : " value="{{ old('date_fin') }}" />

            {{-- Description ou commentaire sur le tarif --}}
            <x-form.input type="text" name="description" label="Description / Remarques : " value="{{ old('description') }}" />

            {{-- Sélection du statut du tarif (Actif / Inactif) --}}
            <div class="mb-3">
                <label for="statut" class="form-label fw-bold">Statut : <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i data-lucide="toggle-on"></i>
                    </span>
                    <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', '1') == (string)$key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('statut')
                    <div class="invalid-feedback d-block mt-1">
                        <i data-lucide="alert-triangle" class="me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Bouton de soumission du formulaire pour valider la création du tarif --}}
            <button type="submit" class="btn btn-outline-primary">Enregistrer le tarif</button>
        </form>
    </section>
@endsection

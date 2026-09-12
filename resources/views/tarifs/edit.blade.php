@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Modification du Tarif')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de modification avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier le Tarif #{{ $tarif->id }} - {{ $tarif->service->nom ?? '' }}</h1>
            {{-- Bouton pour revenir à la liste générale des tarifs --}}
            <a href="{{ route('tarifs.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de mise à jour des informations d'un tarif --}}
        <form action="{{ route('tarifs.update', $tarif) }}" method="post">
            {{-- Jeton de protection CSRF --}}
            @csrf
            {{-- Directive Blade simulant l'envoi de la méthode HTTP PUT --}}
            @method('PUT')

            {{-- Affichage du récapitulatif des erreurs de validation s'il y en a --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ de recherche autocomplété pour le service médical --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche autocomplété --}}
                <label for="service_search_input" class="form-label">Service Médical (Recherche par nom ou code) : </label>

                {{-- Champ texte pré-rempli avec le nom et le code du service actuel --}}
                <input type="text"
                       id="service_search_input"
                       class="form-control @error('service_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le code du service..."
                       data-url="{{ route('services.search') }}"
                       value="{{ old('service_name', $selectedService ? $selectedService->nom . ' (' . $selectedService->code . ')' : '') }}"
                       autocomplete="off">

                {{-- Champ caché stockant l'ID du service sélectionné --}}
                <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id', $tarif->service_id) }}">

                {{-- Conteneur dynamique des suggestions de recherche --}}
                <div id="service_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Affichage de l'erreur si la validation du service échoue --}}
                @error('service_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Saisie du tarif normal pré-rempli --}}
            <x-form.input type="number" name="tarif_normal" label="Tarif Normal (FCFA) : " value="{{ old('tarif_normal', $tarif->tarif_normal) }}" />

            {{-- Saisie du tarif AMO pré-rempli --}}
            <x-form.input type="number" name="tarif_amo" label="Tarif AMO (FCFA) : " value="{{ old('tarif_amo', $tarif->tarif_amo) }}" />

            {{-- Saisie du tarif spécifique pré-rempli --}}
            <x-form.input type="number" name="tarif_specifique" label="Tarif Spécifique (FCFA) : " value="{{ old('tarif_specifique', $tarif->tarif_specifique) }}" />

            {{-- Saisie de la date de début d'application --}}
            <x-form.input type="date" name="date_debut" label="Date de début d'effet : " value="{{ old('date_debut', $tarif->date_debut ? $tarif->date_debut->format('Y-m-d') : '') }}" />

            {{-- Saisie de la date de fin d'application --}}
            <x-form.input type="date" name="date_fin" label="Date de fin d'effet : " value="{{ old('date_fin', $tarif->date_fin ? $tarif->date_fin->format('Y-m-d') : '') }}" />

            {{-- Saisie de la description ou remarques --}}
            <x-form.input type="text" name="description" label="Description / Remarques : " value="{{ old('description', $tarif->description) }}" />

            {{-- Sélection du statut avec la valeur active actuelle pré-sélectionnée --}}
            <div class="mb-3">
                <label for="statut" class="form-label fw-bold">Statut : <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-toggle-on"></i>
                    </span>
                    <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $tarif->statut ? '1' : '0') == (string)$key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('statut')
                    <div class="invalid-feedback d-block mt-1">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Bouton pour enregistrer les modifications apportées au tarif --}}
            <button type="submit" class="btn btn-warning">Mettre à jour le tarif</button>
        </form>
    </section>
@endsection

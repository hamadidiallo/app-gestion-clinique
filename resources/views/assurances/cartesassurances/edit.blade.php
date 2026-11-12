@extends('layout')

{{-- Titre de la page de modification d'une carte d'assurance --}}
@section('title', 'Modification Carte d\'Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier la Carte : {{ $carteassurance->reference }}</h1>
            {{-- Bouton pour retourner à la liste des cartes d'assurance --}}
            <a href="{{ route('cartesassurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de modification d'une carte d'assurance soumis en méthode PUT --}}
        <form action="{{ route('cartesassurances.update', $carteassurance) }}" method="post">
            {{-- Jeton de sécurité CSRF --}}
            @csrf

            {{-- Directive Blade pour simuler la méthode HTTP PUT --}}
            @method('PUT')

            {{-- Affichage du résumé des erreurs de validation --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ de recherche autocomplété pour le patient pré-rempli --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche --}}
                <label for="patient_search_input" class="form-label">Patient (Recherche par nom ou prénom) : </label>

                {{-- Champ de saisie texte pré-rempli avec le nom complet du patient actuel --}}
                <input type="text"
                       id="patient_search_input"
                       class="form-control @error('patient_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le prénom du patient..."
                       data-url="{{ route('patients.search') }}"
                       value="{{ old('patient_name', $carteassurance->patient ? $carteassurance->patient->prenom . ' ' . $carteassurance->patient->nom : '') }}"
                       autocomplete="off">

                {{-- Champ caché contenant l'ID du patient --}}
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $carteassurance->patient_id) }}">

                {{-- Conteneur flottant des résultats de la recherche --}}
                <div id="patient_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Affichage de l'erreur si la validation du patient échoue --}}
                @error('patient_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sélection de la compagnie d'assurance associée --}}
            <x-form.input type="select" name="assurance_id" label="Compagnie d'assurance : " :options="$assurances" value="{{ old('assurance_id', $carteassurance->assurance_id) }}" />

            {{-- Modification de la référence de la carte --}}
            <x-form.input type="text" name="reference" label="Référence de la carte : " value="{{ old('reference', $carteassurance->reference) }}" />

            {{-- Modification du taux de couverture --}}
            <x-form.input type="number" name="taux_couverture" label="Taux de couverture (%) : " value="{{ old('taux_couverture', $carteassurance->taux_couverture) }}" />

            {{-- Modification de la date de début de validité --}}
            <x-form.input type="date" name="date_debut" label="Date de début de validité : " value="{{ old('date_debut', $carteassurance->date_debut ? $carteassurance->date_debut->format('Y-m-d') : '') }}" />

            {{-- Modification de la date de fin de validité --}}
            <x-form.input type="date" name="date_fin" label="Date de fin de validité : " value="{{ old('date_fin', $carteassurance->date_fin ? $carteassurance->date_fin->format('Y-m-d') : '') }}" />

            {{-- Sélection du statut (Actif / Inactif) --}}
            <x-form.input type="select" name="statut" label="Statut : " :options="$statuts" value="{{ old('statut', $carteassurance->statut ? '1' : '0') }}" />

            {{-- Bouton pour valider et mettre à jour la carte d'assurance --}}
            <button type="submit" class="btn btn-warning">Mettre à jour la carte d'assurance</button>
        </form>
    </section>
@endsection

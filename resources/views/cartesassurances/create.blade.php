@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Création Carte d\'Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de retour vers la liste --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Création d'une Carte d'Assurance</h1>
            {{-- Bouton pour retourner à la liste des cartes d'assurance --}}
            <a href="{{ route('cartesassurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de création d'une carte d'assurance --}}
        <form action="{{ route('cartesassurances.store') }}" method="post">
            {{-- Jeton CSRF pour la sécurité de la requête --}}
            @csrf

            {{-- Bloc d'affichage des erreurs de validation --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ de recherche autocomplété pour le patient par nom/prénom --}}
            <div class="mb-3 position-relative">
                {{-- Étiquette du champ de recherche --}}
                <label for="patient_search_input" class="form-label">Patient (Recherche par nom ou prénom) : </label>

                {{-- Champ de saisie texte déclenchant l'autocomplétion JavaScript --}}
                <input type="text"
                       id="patient_search_input"
                       class="form-control @error('patient_id') is-invalid @enderror"
                       placeholder="Tapez le nom ou le prénom du patient..."
                       data-url="{{ route('patients.search') }}"
                       value="{{ old('patient_name', isset($selectedPatient) ? $selectedPatient->nom . ' ' . $selectedPatient->prenom : '') }}"
                       autocomplete="off">

                {{-- Champ caché stockant l'ID du patient sélectionné pour l'envoi du formulaire --}}
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', isset($selectedPatient) ? $selectedPatient->id : '') }}">

                {{-- Conteneur dynamique des suggestions de patients --}}
                <div id="patient_results_list" class="list-group position-absolute w-100 shadow z-3" style="display: none;"></div>

                {{-- Affichage du message d'erreur si la validation du patient échoue --}}
                @error('patient_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sélection de la compagnie d'assurance partenaire --}}
            <x-form.input type="select" name="assurance_id" label="Compagnie d'assurance : " :options="$assurances" value="{{ old('assurance_id') }}" />

            {{-- Saisie de la référence unique de la carte d'assurance --}}
            <x-form.input type="text" name="reference" label="Référence de la carte : " value="{{ old('reference') }}" />

            {{-- Saisie du taux de couverture en pourcentage --}}
            <x-form.input type="number" name="taux_couverture" label="Taux de couverture (%) : " value="{{ old('taux_couverture') }}" />

            {{-- Saisie de la date de début de validité --}}
            <x-form.input type="date" name="date_debut" label="Date de début de validité : " value="{{ old('date_debut') }}" />

            {{-- Saisie de la date de fin de validité --}}
            <x-form.input type="date" name="date_fin" label="Date de fin de validité : " value="{{ old('date_fin') }}" />

            {{-- Sélection du statut de la carte (Actif / Inactif) --}}
            <x-form.input type="select" name="statut" label="Statut : " :options="$statuts" value="{{ old('statut', '1') }}" />

            {{-- Bouton de soumission du formulaire --}}
            <button type="submit" class="btn btn-outline-warning">Créer la carte d'assurance</button>
        </form>
    </section>
@endsection

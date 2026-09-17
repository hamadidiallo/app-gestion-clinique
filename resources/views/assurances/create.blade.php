@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Création Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de création avec le titre et le bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Création d'une Compagnie d'Assurance</h1>
            {{-- Bouton pour retourner à la liste des assurances --}}
            <a href="{{ route('assurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de création d'une compagnie d'assurance --}}
        <form action="{{ route('assurances.store') }}" method="post">
            {{-- Jeton CSRF pour sécuriser la soumission du formulaire --}}
            @csrf

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

            {{-- Champ pour la saisie du nom de la compagnie d'assurance --}}
            <x-form.input type="text" name="nom" label="Nom de l'assurance : " value="{{ old('nom') }}" />

            {{-- Champ pour la saisie du code unique d'assurance --}}
            <x-form.input type="text" name="code" label="Code identifiant (ex: AMO, INPS) : " value="{{ old('code') }}" />

            {{-- Champ pour le taux de prise en charge par défaut (%) --}}
            <x-form.input type="number" name="taux_par_defaut" label="Taux de prise en charge par défaut (%) : " value="{{ old('taux_par_defaut', 80) }}" min="0" max="100" step="1" />

            {{-- Champ pour la saisie du numéro de téléphone --}}
            <x-form.input type="text" name="telephone" label="Téléphone : " value="{{ old('telephone') }}" />

            {{-- Champ pour la saisie de l'adresse email --}}
            <x-form.input type="email" name="email" label="Adresse Email : " value="{{ old('email') }}" />

            {{-- Champ pour la saisie de l'adresse physique (textarea) --}}
            <x-form.input type="text" name="adresse" label="Adresse physique : " value="{{ old('adresse') }}" />

            {{-- Liste déroulante pour le statut (Actif / Inactif) --}}
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

            {{-- Bouton de soumission pour créer la compagnie d'assurance --}}
            <button type="submit" class="btn btn-outline-warning">Créer l'assurance</button>
        </form>
    </section>
@endsection

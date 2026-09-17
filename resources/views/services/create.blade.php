@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Création d\'un Service Médical')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de création avec le titre et le bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Création d'un Service Médical</h1>
            {{-- Bouton pour retourner à la liste des services --}}
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de création d'un service --}}
        <form action="{{ route('services.store') }}" method="post">
            {{-- Jeton CSRF pour sécuriser la soumission du formulaire --}}
            @csrf

            {{-- Affichage des messages d'erreur globaux en cas de non-respect de la validation --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ pour le nom du service médical --}}
            <x-form.input type="text" name="nom" label="Nom du service : " value="{{ old('nom') }}" />

            {{-- Champ pour le code unique du service --}}
            <x-form.input type="text" name="code" label="Code du service : " value="{{ old('code') }}" />

            {{-- Champ texte pour la description facultative du service --}}
            <x-form.input type="text" name="description" label="Description du service : " value="{{ old('description') }}" />

            {{-- Liste déroulante pour la sélection du statut (Actif / Inactif) --}}
            <div class="mb-3">
                <label for="statut" class="form-label fw-bold">Statut du Service : <span class="text-danger">*</span></label>
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

            {{-- Bouton de soumission du formulaire pour créer le service --}}
            <button type="submit" class="btn btn-outline-primary">Créer le service</button>
        </form>
    </section>
@endsection

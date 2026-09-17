@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Édition du Service Médical')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de modification avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier le Service : {{ $service->nom }}</h1>
            {{-- Bouton pour retourner à la liste des services --}}
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de mise à jour du service --}}
        <form action="{{ route('services.update', $service) }}" method="post">
            {{-- Jeton de sécurité CSRF --}}
            @csrf
            {{-- Directive Blade simulant la méthode HTTP PUT pour la mise à jour --}}
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

            {{-- Champ pour modifier le nom du service --}}
            <x-form.input type="text" name="nom" label="Nom du service : " value="{{ old('nom', $service->nom) }}" />

            {{-- Champ pour modifier le code du service --}}
            <x-form.input type="text" name="code" label="Code du service : " value="{{ old('code', $service->code) }}" />

            {{-- Champ texte pour la description --}}
            <x-form.input type="text" name="description" label="Description du service : " value="{{ old('description', $service->description) }}" />

            {{-- Sélection du statut du service avec valeur courante sélectionnée (Actif / Inactif) --}}
            <div class="mb-3">
                <label for="statut" class="form-label fw-bold">Statut du Service : <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i data-lucide="toggle-on"></i>
                    </span>
                    <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $service->statut ? '1' : '0') == (string)$key ? 'selected' : '' }}>
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

            {{-- Bouton pour enregistrer les modifications --}}
            <button type="submit" class="btn btn-warning">Mettre à jour le service</button>
        </form>
    </section>
@endsection

@extends('layout')

{{-- Titre de la page de modification d'une assurance --}}
@section('title', 'Modification Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier l'Assurance : {{ $assurance->nom }}</h1>
            {{-- Bouton pour retourner à la liste des assurances --}}
            <a href="{{ route('assurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de modification d'assurance soumis en méthode PUT --}}
        <form action="{{ route('assurances.update', $assurance) }}" method="post">
            {{-- Jeton de sécurité CSRF --}}
            @csrf

            {{-- Directive Blade pour simuler la méthode HTTP PUT --}}
            @method('PUT')

            {{-- Affichage des erreurs de validation --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ pour la modification du nom --}}
            <x-form.input type="text" name="nom" label="Nom de l'assurance : " value="{{ old('nom', $assurance->nom) }}" />

            {{-- Champ pour la modification du code d'assurance --}}
            <x-form.input type="text" name="code" label="Code identifiant (ex: AMO, INPS) : " value="{{ old('code', $assurance->code) }}" />

            {{-- Champ pour la modification du taux par défaut (%) --}}
            <x-form.input type="number" name="taux_par_defaut" label="Taux de prise en charge par défaut (%) : " value="{{ old('taux_par_defaut', $assurance->taux_par_defaut ?? 80) }}" min="0" max="100" step="1" />

            {{-- Champ pour la modification du numéro de téléphone --}}
            <x-form.input type="text" name="telephone" label="Téléphone : " value="{{ old('telephone', $assurance->telephone) }}" />

            {{-- Champ pour la modification de l'adresse email --}}
            <x-form.input type="email" name="email" label="Adresse Email : " value="{{ old('email', $assurance->email) }}" />

            {{-- Champ pour la modification de l'adresse physique --}}
            <x-form.input type="textarea" name="adresse" label="Adresse physique : " value="{{ old('adresse', $assurance->adresse) }}" />

            {{-- Liste déroulante du statut avec la valeur actuelle pré-sélectionnée --}}
            <div class="mb-3">
                <label for="statut" class="form-label fw-bold">Statut : <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i data-lucide="toggle-on"></i>
                    </span>
                    <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $assurance->statut ? '1' : '0') == (string)$key ? 'selected' : '' }}>
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
            <button type="submit" class="btn btn-warning">Mettre à jour l'assurance</button>
        </form>
    </section>
@endsection

@extends('layout')

{{-- Titre de la page dans le navigateur --}}
@section('title', 'Modifier le Médecin')

{{-- Contenu de la vue de modification --}}
@section('content')
    <section class="mt-4">
        {{-- Titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier le Médecin : Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</h1>
            <a href="{{ route('medecins.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire d'édition --}}
        <form action="{{ route('medecins.update', $medecin) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Champ Nom --}}
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $medecin->nom) }}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Prénom --}}
                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $medecin->prenom) }}" required>
                    @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Téléphone --}}
                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $medecin->telephone) }}" required>
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Spécialité --}}
                <div class="col-md-6">
                    <label for="specialite" class="form-label">Spécialité <span class="text-danger">*</span></label>
                    <input type="text" name="specialite" id="specialite" class="form-control @error('specialite') is-invalid @enderror" value="{{ old('specialite', $medecin->specialite) }}" required>
                    @error('specialite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Type de Rémunération --}}
                <div class="col-md-4">
                    <label for="type_remuneration" class="form-label">Type de Rémunération <span class="text-danger">*</span></label>
                    <select name="type_remuneration" id="type_remuneration" class="form-select @error('type_remuneration') is-invalid @enderror" required>
                        <option value="pourcentage" {{ old('type_remuneration', $medecin->type_remuneration) == 'pourcentage' ? 'selected' : '' }}>Pourcentage (Acte)</option>
                        <option value="fixe" {{ old('type_remuneration', $medecin->type_remuneration) == 'fixe' ? 'selected' : '' }}>Salaire Fixe</option>
                        <option value="mixte" {{ old('type_remuneration', $medecin->type_remuneration) == 'mixte' ? 'selected' : '' }}>Mixte (Fixe + Pourcentage)</option>
                    </select>
                    @error('type_remuneration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Pourcentage --}}
                <div class="col-md-4">
                    <label for="pourcentage" class="form-label">Pourcentage (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="pourcentage" id="pourcentage" class="form-control @error('pourcentage') is-invalid @enderror" value="{{ old('pourcentage', $medecin->pourcentage) }}">
                    @error('pourcentage')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Salaire Fixe --}}
                <div class="col-md-4">
                    <label for="salaire_fixe" class="form-label">Salaire Fixe (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="salaire_fixe" id="salaire_fixe" class="form-control @error('salaire_fixe') is-invalid @enderror" value="{{ old('salaire_fixe', $medecin->salaire_fixe) }}">
                    @error('salaire_fixe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Champ Statut --}}
                <div class="col-md-12">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $medecin->statut ? '1' : '0') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Bouton de validation --}}
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection

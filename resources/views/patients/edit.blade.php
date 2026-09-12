@extends('layout')

{{-- Titre de la page de modification d'un patient --}}
@section('title', 'Modification Patient')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre de modification et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier le Patient : {{ $patient->prenom }} {{ $patient->nom }}</h1>
            {{-- Bouton pour retourner à la liste des patients --}}
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de modification d'un patient soumis en méthode PUT --}}
        <form action="{{ route('patients.update', $patient) }}" method="post">
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

            {{-- Champ pour la modification du prénom --}}
            <x-form.input type="text" name="prenom" label="Prénom : " value="{{ old('prenom', $patient->prenom) }}" />

            {{-- Champ pour la modification du nom --}}
            <x-form.input type="text" name="nom" label="Nom : " value="{{ old('nom', $patient->nom) }}" />

            {{-- Liste déroulante pour le sexe pré-sélectionnant la valeur actuelle --}}
            <x-form.input type="select" name="sexe" label="Sexe : " :options="$sexes" value="{{ old('sexe', $patient->sexe) }}" />

            {{-- Champ pour la modification du numéro de téléphone --}}
            <x-form.input type="text" name="telephone" label="Numéro de téléphone : " value="{{ old('telephone', $patient->telephone) }}" />

            {{-- Liste déroulante pour le statut d'assurance pré-sélectionnant la valeur actuelle --}}
            <div class="mb-3">
                <label for="statut" class="form-label font-weight-bold">Statut d'assurance : </label>
                <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                    <option value="non_assure" {{ old('statut', $patient->statut) == 'non_assure' ? 'selected' : '' }}>Non Assuré (Paiement direct)</option>
                    <option value="assure" {{ old('statut', $patient->statut) == 'assure' ? 'selected' : '' }}>Assuré (Tiers Payant)</option>
                </select>
                @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Bloc d'informations d'assurance (Affiché dynamiquement si statut = assure) --}}
            <div id="assurance_fields_box" class="card border-info mb-4 bg-light shadow-sm" style="display: {{ old('statut', $patient->statut) == 'assure' ? 'block' : 'none' }};">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-shield-check me-2"></i>Informations de la Carte d'Assurance
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="assurance_id" class="form-label fw-bold text-dark">Compagnie d'Assurance Partner : </label>
                            <select name="assurance_id" id="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                                <option value="">-- Sélectionner une compagnie --</option>
                                @foreach($assurances as $assurance)
                                    <option value="{{ $assurance->id }}" {{ old('assurance_id', $carteAssurance->assurance_id ?? '') == $assurance->id ? 'selected' : '' }}>
                                        {{ $assurance->nom }} ({{ $assurance->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assurance_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label for="carte_reference" class="form-label fw-bold text-dark">N° / Matricule Carte : </label>
                            <input type="text" name="carte_reference" id="carte_reference" class="form-control @error('carte_reference') is-invalid @enderror" value="{{ old('carte_reference', $carteAssurance->reference ?? '') }}" placeholder="ex: ASS-2026-99">
                            @error('carte_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label for="taux_couverture" class="form-label fw-bold text-dark">Taux de Couverture (%) : </label>
                            <div class="input-group">
                                <input type="number" step="1" min="0" max="100" name="taux_couverture" id="taux_couverture" class="form-control text-success fw-bold @error('taux_couverture') is-invalid @enderror" value="{{ old('taux_couverture', $carteAssurance->taux_couverture ?? 80) }}" placeholder="80">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">%</span>
                            </div>
                            @error('taux_couverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bouton de soumission pour appliquer la mise à jour --}}
            <div class="text-end">
                <button type="submit" class="btn btn-warning btn-lg fw-bold px-4">
                    <i class="bi bi-save me-2"></i>Mettre à jour le patient
                </button>
            </div>
        </form>
    </section>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectStatut = document.getElementById('statut');
            const boxAssurance = document.getElementById('assurance_fields_box');

            if (selectStatut && boxAssurance) {
                function toggleAssuranceFields() {
                    if (selectStatut.value === 'assure') {
                        boxAssurance.style.display = 'block';
                    } else {
                        boxAssurance.style.display = 'none';
                    }
                }
                selectStatut.addEventListener('change', toggleAssuranceFields);
                toggleAssuranceFields();
            }
        });
    </script>
@endsection

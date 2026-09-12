@extends('layout')

@section('title', 'Création d\'un Patient - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-person-plus-fill me-2"></i>Nouveau Dossier Patient
            </h1>
            <p class="text-muted mb-0">Enregistrement d'un nouveau patient dans le système de la clinique.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Retour au répertoire
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-card-checklist text-primary me-2"></i>Fiche d'Inscription Administrative
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('patients.store') }}" method="post">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="prenom" 
                            label="Prénom du Patient" 
                            icon="bi-person" 
                            placeholder="Ex: Jean-Baptiste" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="nom" 
                            label="Nom de famille" 
                            icon="bi-person-badge" 
                            placeholder="Ex: NIKIZA" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="select" 
                            name="sexe" 
                            label="Sexe / Genre" 
                            icon="bi-gender-ambiguous" 
                            :options="$sexes" 
                            placeholder="-- Sélectionner le genre --" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="telephone" 
                            label="Numéro de téléphone" 
                            icon="bi-telephone" 
                            placeholder="Ex: +257 79 000 000" 
                        />
                    </div>

                    <div class="col-md-12">
                        <x-form.input 
                            type="select" 
                            name="statut" 
                            id="statut"
                            label="Statut de prise en charge" 
                            icon="bi-shield-check" 
                            :options="[
                                'non_assure' => 'Non Assuré (Paiement direct en caisse)',
                                'assure' => 'Assuré (Prise en charge par Tiers Payant)'
                            ]" 
                            :required="true" 
                        />
                    </div>
                </div>

                {{-- Bloc d'informations d'assurance --}}
                <div id="assurance_fields_box" class="card border-info my-4 bg-light shadow-sm" style="display: {{ old('statut') == 'assure' ? 'block' : 'none' }};">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-shield-lock me-2"></i>Détails de la Carte d'Assurance
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="assurance_id" class="form-label fw-bold text-dark fs-7 mb-1">Compagnie d'Assurance Partenaire</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-building"></i></span>
                                    <select name="assurance_id" id="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                                        <option value="">-- Sélectionner un organisme --</option>
                                        @foreach($assurances as $assurance)
                                            <option value="{{ $assurance->id }}" {{ old('assurance_id') == $assurance->id ? 'selected' : '' }}>
                                                {{ $assurance->nom }} ({{ $assurance->code ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('assurance_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <x-form.input 
                                    type="text" 
                                    name="carte_reference" 
                                    label="N° / Matricule Carte" 
                                    icon="bi-card-heading" 
                                    placeholder="Ex: ASS-2026-99" 
                                />
                            </div>

                            <div class="col-md-3">
                                <x-form.input 
                                    type="number" 
                                    name="taux_couverture" 
                                    label="Taux de Couverture" 
                                    icon="bi-percent" 
                                    suffix="%" 
                                    value="80"
                                    min="0"
                                    max="100"
                                    step="1"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Enregistrer le patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
@endpush
@endsection

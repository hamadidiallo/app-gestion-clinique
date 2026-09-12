@extends('layout')

@section('title', 'Nouveau Médecin - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-person-badge me-2"></i>Ajouter un Nouveau Médecin
            </h1>
            <p class="text-muted mb-0">Création de la fiche d'un praticien médical et configuration de sa rémunération.</p>
        </div>
        <a href="{{ route('medecins.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-person-lines-fill text-primary me-2"></i>Fiche Praticien
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('medecins.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="prenom" 
                            label="Prénom" 
                            icon="bi-person" 
                            placeholder="Ex: Gabriel" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="nom" 
                            label="Nom de famille" 
                            icon="bi-person-badge" 
                            placeholder="Ex: NDUWIMANA" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="telephone" 
                            label="Numéro de Téléphone" 
                            icon="bi-telephone" 
                            placeholder="Ex: +257 79 111 222" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="specialite" 
                            label="Spécialité Médicale" 
                            icon="bi-journal-medical" 
                            placeholder="Ex: Médecine Générale, Pédiatrie, Gynécologie..." 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="select" 
                            name="type_remuneration" 
                            label="Type de Rémunération" 
                            icon="bi-calculator" 
                            :options="[
                                'pourcentage' => 'Pourcentage sur les actes',
                                'fixe' => 'Salaire Fixe mensuel',
                                'mixte' => 'Mixte (Fixe + Pourcentage)'
                            ]"
                            placeholder="-- Sélectionner --"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="number" 
                            name="pourcentage" 
                            label="Pourcentage Rétrocession" 
                            icon="bi-percent" 
                            suffix="%"
                            step="0.1"
                            min="0"
                            max="100"
                            placeholder="Ex: 40" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="number" 
                            name="salaire_fixe" 
                            label="Base Fixe Mensuelle" 
                            icon="bi-cash" 
                            suffix="FBU"
                            step="1"
                            min="0"
                            placeholder="Ex: 500000" 
                        />
                    </div>

                    <div class="col-md-12">
                        <x-form.input 
                            type="select" 
                            name="statut" 
                            label="Statut du Médecin" 
                            icon="bi-toggle-on" 
                            :options="$statuts" 
                            value="1"
                            :required="true" 
                        />
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Enregistrer le médecin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layout')

@section('title', 'Centre de Rapports & Statistiques - CLINGEST')

@section('content')
<div class="container py-4">
    {{-- En-tête de la page des rapports --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold">📊 Centre de Rapports & Analytique</h1>
            <p class="text-muted">Générez et consultez les états financiers, relevés d'assurances et récapitulatifs d'honoraires.</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                ⬅ Retour au Tableau de Bord
            </a>
        </div>
    </div>

    {{-- Grille des modules de rapports disponibles --}}
    <div class="row g-4">
        {{-- Card Rapport Assurances (Tiers Payant) --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fs-1 me-3">🛡️</span>
                        <div>
                            <h5 class="card-title text-dark mb-1">Relevé Détaillé Assurances (Tiers Payant)</h5>
                            <span class="badge bg-primary">Facturation & Prise en Charge</span>
                        </div>
                    </div>
                    <p class="card-text text-secondary">
                        Rapport officiel de justification à destination des organismes d'assurance. 
                        Affiche pour chaque acte le nom, prénom, sexe du patient, la date de visite, l'acte réalisé, 
                        le tarif public, le taux de prise en charge et le montant dû par l'assurance.
                    </p>
                    <a href="{{ route('rapports.assurances') }}" class="btn btn-primary w-100 mt-2">
                        Consulter le Relevé Assurances ➔
                    </a>
                </div>
            </div>
        </div>

        {{-- Card Rapport Rétrocession Médecins --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fs-1 me-3">👨‍⚕️</span>
                        <div>
                            <h5 class="card-title text-dark mb-1">Rétrocession & Honoraires Médecins</h5>
                            <span class="badge bg-success">Rémunération & Ratios</span>
                        </div>
                    </div>
                    <p class="card-text text-secondary">
                        Relevé individuel et collectif des actes effectués par les médecins prestataires. 
                        Calcul automatique de la part revenant au médecin et de la part conservée par la clinique selon la grille tarifaire.
                    </p>
                    <a href="{{ route('rapports.medecins') }}" class="btn btn-success w-100 mt-2">
                        Consulter les Honoraires Médecins ➔
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

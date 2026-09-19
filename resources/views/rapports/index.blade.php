@extends('layout')

@section('title', 'Centre de Rapports & Analytique - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de la page des rapports --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Tableau de bord
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-light text-dark border small font-mono">Analytique Clinique</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="bar-chart-3" class="lucide"></i>
                </div>
                <span>Centre de Rapports & États Financiers</span>
            </h1>
            <p class="text-muted mb-0 small">
                Générez, analysez et exportez les états de facturation officielle, relevés d'assurances et décomptes d'honoraires praticiens.
            </p>
        </div>
    </div>

    {{-- Grille des modules de rapports disponibles --}}
    <div class="row g-4">
        {{-- Card Rapport Assurances (Tiers Payant) --}}
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 bg-white p-4 transition-all hover-shadow">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary-subtle text-primary rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                        <i data-lucide="shield-check" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Relevé Détaillé Assurances (Tiers Payant)</h5>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">
                            Recouvrement & Prise en Charge
                        </span>
                    </div>
                </div>
                <p class="text-secondary small mb-4 lh-base">
                    Bordereau officiel de justification de facturation à destination des mutuelles et organismes d'assurance. 
                    Détaille pour chaque acte le patient (nom, sexe, matricule), la date de visite, l'acte réalisé, le tarif conventionné brut, 
                    le taux (%) et le montant de la créance à recouvrer.
                </p>
                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted d-inline-flex align-items-center gap-1">
                        <i data-lucide="printer" class="lucide-sm text-muted"></i>
                        Format A4 & Export Excel/PDF
                    </span>
                    <a href="{{ route('rapports.assurances') }}" class="btn btn-primary fw-bold px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                        <span>Ouvrir le Bordereau</span>
                        <i data-lucide="arrow-right" class="lucide-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card Rapport Rétrocession Médecins --}}
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 bg-white p-4 transition-all hover-shadow">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-teal-subtle text-teal rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                        <i data-lucide="stethoscope" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Rétrocession & Honoraires Médecins</h5>
                        <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2 py-1 rounded-pill small">
                            Rémunération & Parts Clinique
                        </span>
                    </div>
                </div>
                <p class="text-secondary small mb-4 lh-base">
                    Relevé individuel et collectif des actes exécutés par les médecins prestataires. 
                    Ventilation automatique entre la quote-part revenant au médecin (pourcentage ou forfait) et la quote-part conservée par l'établissement.
                </p>
                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted d-inline-flex align-items-center gap-1">
                        <i data-lucide="file-check-2" class="lucide-sm text-muted"></i>
                        Suivi des fiches de paie
                    </span>
                    <a href="{{ route('rapports.medecins') }}" class="btn btn-teal text-white fw-bold px-4 d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: var(--primary-color);">
                        <span>Consulter les Honoraires</span>
                        <i data-lucide="arrow-right" class="lucide-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

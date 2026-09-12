@extends('layout')

@section('title', 'Détails de la Rémunération')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fiche de Paie : Dr. {{ $remuneration->medecin->prenom ?? '' }} {{ $remuneration->medecin->nom ?? '' }}</h1>
            <div>
                <a href="{{ route('remunerations.edit', $remuneration) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('remunerations.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Décompte Financier</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Médecin Bénéficiaire :</strong> Dr. {{ $remuneration->medecin->prenom ?? '' }} {{ $remuneration->medecin->nom ?? '' }}</div>
                    <div class="col-md-6 mb-3"><strong>Agent Validateur :</strong> {{ $remuneration->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Période du :</strong> {{ $remuneration->periode_debut ? $remuneration->periode_debut->format('d/m/Y') : '' }} au {{ $remuneration->periode_fin ? $remuneration->periode_fin->format('d/m/Y') : '' }}</div>
                    <div class="col-md-6 mb-3"><strong>Type de Rémunération :</strong> <span class="badge bg-secondary">{{ ucfirst($remuneration->type_remuneration) }}</span></div>
                    <div class="col-md-4 mb-3"><strong>Montant Base Actes :</strong> {{ number_format($remuneration->montant_base, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Part Net Médecin :</strong> <span class="text-success fw-bold fs-5">{{ number_format($remuneration->montant_medecin, 2, ',', ' ') }} FCFA</span></div>
                    <div class="col-md-4 mb-3"><strong>Part Retenue Clinique :</strong> {{ number_format($remuneration->montant_clinique, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-6 mb-3"><strong>Date Règlement :</strong> {{ $remuneration->date_paiement ? $remuneration->date_paiement->format('d/m/Y') : 'Non réglé' }}</div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($remuneration->statut == 'paye')
                            <span class="badge bg-success">Payé</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($remuneration->statut) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

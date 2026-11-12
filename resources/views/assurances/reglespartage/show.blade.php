@extends('layout')

@section('title', 'Détails de la Règle de Partage')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Règle de Partage pour : {{ $reglespartage->service->nom ?? 'N/A' }}</h1>
            <div>
                <a href="{{ route('reglespartage.edit', $reglespartage) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('reglespartage.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Paramètres de Rétrocession</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Service Médical :</strong> {{ $reglespartage->service->nom ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Part Médecin :</strong> <span class="badge bg-primary fs-6">{{ $reglespartage->pourcentage_medecin }} %</span></div>
                    <div class="col-md-6 mb-3"><strong>Part Clinique :</strong> <span class="badge bg-info text-dark fs-6">{{ $reglespartage->pourcentage_clinique }} %</span></div>
                    <div class="col-md-6 mb-3"><strong>Date Début :</strong> {{ $reglespartage->date_debut ? $reglespartage->date_debut->format('d/m/Y') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Date Fin :</strong> {{ $reglespartage->date_fin ? $reglespartage->date_fin->format('d/m/Y') : 'Permanente' }}</div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($reglespartage->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3"><strong>Description :</strong><p class="text-muted mt-1">{{ $reglespartage->description ?? 'Aucune description.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

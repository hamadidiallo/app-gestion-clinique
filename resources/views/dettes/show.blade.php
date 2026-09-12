@extends('layout')

@section('title', 'Détails de la Dette')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fiche de Dette #{{ $dette->id }}</h1>
            <div>
                <a href="{{ route('dettes.edit', $dette) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('dettes.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Informations de la Créance</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Ticket Parent :</strong> <code>{{ $dette->ticket ? $dette->ticket->reference : 'N/A' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Patient Débiteur :</strong> {{ $dette->patient ? $dette->patient->nom . ' ' . $dette->patient->prenom : 'N/A' }}</div>
                    <div class="col-md-4 mb-3"><strong>Créance Initiale :</strong> {{ number_format($dette->montant_initial, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Montant Réglé :</strong> {{ number_format($dette->montant_paye, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Reste à Recouvrer :</strong> <span class="text-danger fw-bold fs-5">{{ number_format($dette->reste_a_payer, 2, ',', ' ') }} FCFA</span></div>
                    <div class="col-md-6 mb-3"><strong>Date Création :</strong> {{ $dette->date_creation ? $dette->date_creation->format('d/m/Y') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Date Règlement Prévu :</strong> {{ $dette->date_reglement ? $dette->date_reglement->format('d/m/Y') : 'Non définie' }}</div>
                    <div class="col-md-12 mb-3"><strong>Observations :</strong><p class="text-muted mt-1">{{ $dette->description ?? 'Aucune observation.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

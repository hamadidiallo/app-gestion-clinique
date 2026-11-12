@extends('layout')

@section('title', 'Détails du Mode de Paiement')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Mode de Règlement : {{ $modepaiement->nom }}</h1>
            <div>
                <a href="{{ route('modepaiements.edit', $modepaiement) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('modepaiements.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Nom du Mode :</strong> {{ $modepaiement->nom }}</div>
                    <div class="col-md-6 mb-3"><strong>Code Identifiant :</strong> <code>{{ $modepaiement->code }}</code></div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($modepaiement->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3"><strong>Description :</strong><p class="text-muted mt-1">{{ $modepaiement->description ?? 'Aucune description.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

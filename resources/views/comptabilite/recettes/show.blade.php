@extends('layout')

@section('title', 'Détails de la Recette')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Pièce de Recette : <code>{{ $recette->reference }}</code></h1>
            <div>
                <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('recettes.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Informations d'Encaissement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Ticket Parent :</strong> <code>{{ $recette->ticket ? $recette->ticket->reference : 'N/A' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Règlement Lié :</strong> <code>{{ $recette->paiement ? $recette->paiement->reference : 'N/A' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Montant Encaisse :</strong> <strong class="text-success fs-5">{{ number_format($recette->montant, 2, ',', ' ') }} FCFA</strong></div>
                    <div class="col-md-6 mb-3"><strong>Date Recette :</strong> {{ $recette->date_recette ? $recette->date_recette->format('d/m/Y') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Agent Saisisseur :</strong> {{ $recette->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($recette->statut)
                            <span class="badge bg-success">Validée</span>
                        @else
                            <span class="badge bg-danger">Annulée</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3"><strong>Description :</strong><p class="text-muted mt-1">{{ $recette->description ?? 'Aucune observation.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

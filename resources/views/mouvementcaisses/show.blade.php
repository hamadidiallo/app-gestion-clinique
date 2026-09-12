@extends('layout')

@section('title', 'Détails du Mouvement de Caisse')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Mouvement de Caisse : <code>{{ $mouvementCaisse->reference }}</code></h1>
            <div>
                <a href="{{ route('mouvementcaisses.edit', $mouvementCaisse) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('mouvementcaisses.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations du Mouvement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Caisse Cible :</strong> Session #{{ $mouvementCaisse->caisse_id }}</div>
                    <div class="col-md-6 mb-3"><strong>Opérateur :</strong> {{ $mouvementCaisse->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Type :</strong> <span class="badge {{ $mouvementCaisse->type == 'entree' ? 'bg-success' : 'bg-danger' }}">{{ strtoupper($mouvementCaisse->type) }}</span></div>
                    <div class="col-md-6 mb-3"><strong>Origine / Motif :</strong> {{ $mouvementCaisse->origine }}</div>
                    <div class="col-md-6 mb-3"><strong>Montant :</strong> <strong class="fs-5">{{ number_format($mouvementCaisse->montant, 2, ',', ' ') }} FCFA</strong></div>
                    <div class="col-md-6 mb-3"><strong>Date Mouvement :</strong> {{ $mouvementCaisse->date_mouvement ? $mouvementCaisse->date_mouvement->format('d/m/Y H:i') : '-' }}</div>
                    <div class="col-md-12 mb-3"><strong>Description :</strong><p class="text-muted mt-1">{{ $mouvementCaisse->description ?? 'Aucune description.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

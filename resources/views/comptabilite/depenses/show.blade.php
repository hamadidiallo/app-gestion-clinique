@extends('layout')

@section('title', 'Détails de la Dépense')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Pièce de Dépense : <code>{{ $depense->reference }}</code></h1>
            <div>
                <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('depenses.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Informations de la Charge</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Catégorie de Dépense :</strong> {{ $depense->categorieDepense->nom ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Bénéficiaire / Fournisseur :</strong> {{ $depense->beneficiaire }}</div>
                    <div class="col-md-6 mb-3"><strong>Montant :</strong> <strong class="text-danger fs-5">{{ number_format($depense->montant, 2, ',', ' ') }} FCFA</strong></div>
                    <div class="col-md-6 mb-3"><strong>Mode de Règlement :</strong> {{ $depense->modePaiement->nom ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Date Dépense :</strong> {{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Agent Saisisseur :</strong> {{ $depense->user->name ?? 'N/A' }}</div>
                    <div class="col-md-12 mb-3"><strong>Description / Remarques :</strong><p class="text-muted mt-1">{{ $depense->description ?? 'Aucune remarque.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

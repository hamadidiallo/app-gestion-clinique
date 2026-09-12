@extends('layout')

@section('title', 'Détails de la Ligne de Ticket')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Ligne de Ticket #{{ $ticketdetail->id }}</h1>
            <div>
                <a href="{{ route('ticketdetails.edit', $ticketdetail) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('ticketdetails.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations de la Ligne</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Ticket Référence :</strong> <code>{{ $ticketdetail->ticket ? $ticketdetail->ticket->reference : 'N/A' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Prestation :</strong> {{ $ticketdetail->prestation ? $ticketdetail->prestation->nom : 'N/A' }}</div>
                    <div class="col-md-4 mb-3"><strong>Quantité :</strong> {{ $ticketdetail->quantite }}</div>
                    <div class="col-md-4 mb-3"><strong>Prix Unitaire :</strong> {{ number_format($ticketdetail->prix_unitaire, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Montant Total :</strong> {{ number_format($ticketdetail->montant_total, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-6 mb-3"><strong>Part Assurance :</strong> {{ number_format($ticketdetail->montant_assurance, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-6 mb-3"><strong>Part Patient :</strong> {{ number_format($ticketdetail->montant_patient, 2, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </section>
@endsection

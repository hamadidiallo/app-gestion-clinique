@extends('layout')

@section('title', 'Reçu de Paiement')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Reçu de Règlement : <code>{{ $paiement->reference }}</code></h1>
            <div>
                <a href="{{ route('paiements.edit', $paiement) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('paiements.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Récépissé d'Encaissement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Ticket Associé :</strong> <code>{{ $paiement->ticket ? $paiement->ticket->reference : 'N/A' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Patient :</strong> {{ $paiement->ticket && $paiement->ticket->patient ? $paiement->ticket->patient->nom . ' ' . $paiement->ticket->patient->prenom : 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Mode de Règlement :</strong> <span class="badge bg-secondary">{{ $paiement->modePaiement->nom ?? 'N/A' }}</span></div>
                    <div class="col-md-6 mb-3"><strong>Type Payeur :</strong> {{ ucfirst($paiement->type_payeur) }} {{ $paiement->assurance ? '('.$paiement->assurance->nom.')' : '' }}</div>
                    <div class="col-md-4 mb-3"><strong>Montant Donner :</strong> {{ number_format($paiement->montant_recu, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Montant Imputé :</strong> <span class="text-success fw-bold fs-5">{{ number_format($paiement->montant_impute, 2, ',', ' ') }} FCFA</span></div>
                    <div class="col-md-4 mb-3"><strong>Monnaie Rendue :</strong> {{ number_format($paiement->montant_rendu, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-6 mb-3"><strong>Date & Heure :</strong> {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y H:i') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Agent Caissier :</strong> {{ $paiement->user->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection

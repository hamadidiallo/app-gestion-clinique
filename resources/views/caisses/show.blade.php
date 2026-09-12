@extends('layout')

@section('title', 'Détails de la Caisse')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Session de Caisse #{{ $caisse->id }}</h1>
            <div>
                <a href="{{ route('caisses.edit', $caisse) }}" class="btn btn-warning">Clôturer / Modifier</a>
                <a href="{{ route('caisses.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Récapitulatif Financier de la Session</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Caissier Responsable :</strong> {{ $caisse->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6 mb-3"><strong>Statut :</strong> <span class="badge bg-success">{{ strtoupper($caisse->statut) }}</span></div>
                    <div class="col-md-6 mb-3"><strong>Ouverture :</strong> {{ $caisse->date_ouverture ? $caisse->date_ouverture->format('d/m/Y H:i') : '-' }}</div>
                    <div class="col-md-6 mb-3"><strong>Fermeture :</strong> {{ $caisse->date_fermeture ? $caisse->date_fermeture->format('d/m/Y H:i') : 'En cours...' }}</div>
                    <div class="col-md-4 mb-3"><strong>Fond Initial :</strong> {{ number_format($caisse->fonds_initial, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3"><strong>Total Entrées :</strong> <span class="text-success">+{{ number_format($caisse->total_entrees, 2, ',', ' ') }} FCFA</span></div>
                    <div class="col-md-4 mb-3"><strong>Total Sorties :</strong> <span class="text-danger">-{{ number_format($caisse->total_sorties, 2, ',', ' ') }} FCFA</span></div>
                    <div class="col-md-4 mb-3"><strong>Solde Théorique :</strong> <strong class="fs-5">{{ number_format($caisse->solde_theorique, 2, ',', ' ') }} FCFA</strong></div>
                    <div class="col-md-4 mb-3"><strong>Solde Physique Compté :</strong> {{ number_format($caisse->solde_physique ?? 0, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-4 mb-3">
                        <strong>Écart de Caisse :</strong>
                        <span class="fs-5 {{ $caisse->ecart < 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($caisse->ecart ?? 0, 2, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

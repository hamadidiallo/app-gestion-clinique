@extends('layout')

@section('title', 'Détails de l\'Employé')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fiche de l'Employé : {{ $employe->prenom }} {{ $employe->nom }}</h1>
            <div>
                <a href="{{ route('employes.edit', $employe) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('employes.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Personnelles & Professionnelles</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Nom & Prénom :</strong> {{ $employe->prenom }} {{ $employe->nom }}</div>
                    <div class="col-md-6 mb-3"><strong>Fonction :</strong> {{ $employe->fonction }}</div>
                    <div class="col-md-6 mb-3"><strong>Téléphone :</strong> {{ $employe->telephone }}</div>
                    <div class="col-md-6 mb-3"><strong>Email :</strong> {{ $employe->email ?? 'Non renseigné' }}</div>
                    <div class="col-md-6 mb-3"><strong>Type Rémunération :</strong> <span class="badge bg-secondary">{{ ucfirst($employe->type_remuneration) }}</span></div>
                    <div class="col-md-6 mb-3"><strong>Salaire Fixe :</strong> {{ number_format($employe->salaire_fixe, 2, ',', ' ') }} FCFA</div>
                    <div class="col-md-6 mb-3"><strong>Date Embauche :</strong> {{ $employe->date_embauche ? $employe->date_embauche->format('d/m/Y') : 'Non précisée' }}</div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($employe->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3"><strong>Description / Observations :</strong><p class="text-muted mt-1">{{ $employe->description ?? 'Aucune observation.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection

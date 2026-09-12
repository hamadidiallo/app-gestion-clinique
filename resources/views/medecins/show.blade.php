@extends('layout')

{{-- Titre de la page dans le navigateur --}}
@section('title', 'Détails du Médecin')

{{-- Contenu de la vue détaillée du médecin --}}
@section('content')
    <section class="mt-4">
        {{-- Titre de la fiche et boutons d'action --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fiche du Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</h1>
            <div>
                <a href="{{ route('medecins.edit', $medecin) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('medecins.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        {{-- Carte d'informations générales --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nom & Prénom :</strong> Dr. {{ $medecin->prenom }} {{ $medecin->nom }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Spécialité :</strong> {{ $medecin->specialite }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Téléphone :</strong> {{ $medecin->telephone }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Type de Rémunération :</strong> <span class="badge bg-secondary">{{ ucfirst($medecin->type_remuneration) }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Pourcentage d'Acte :</strong> {{ $medecin->pourcentage ? $medecin->pourcentage . ' %' : 'Non défini' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Salaire Fixe :</strong> {{ $medecin->salaire_fixe ? number_format($medecin->salaire_fixe, 2, ',', ' ') . ' FCFA' : 'Non défini' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($medecin->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

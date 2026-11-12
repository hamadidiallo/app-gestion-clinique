@extends('layout')

{{-- Titre de la page d'affichage d'une carte d'assurance --}}
@section('title', 'Détails de la Carte d\'Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails de la Carte : {{ $carteassurance->reference }}</h1>
            {{-- Bouton pour retourner à la liste des cartes d'assurance --}}
            <a href="{{ route('cartesassurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Carte d'affichage des informations de la carte d'assurance --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Fiche Carte d'Assurance #{{ $carteassurance->id }}
            </div>
            <div class="card-body">
                <p><strong>Référence :</strong> {{ $carteassurance->reference }}</p>

                <p>
                    <strong>Patient :</strong>
                    @if ($carteassurance->patient)
                        <a href="{{ route('patients.show', $carteassurance->patient) }}">
                            {{ $carteassurance->patient->prenom }} {{ $carteassurance->patient->nom }}
                        </a>
                    @else
                        Non spécifié
                    @endif
                </p>

                <p>
                    <strong>Compagnie d'assurance :</strong>
                    @if ($carteassurance->assurance)
                        <a href="{{ route('assurances.show', $carteassurance->assurance) }}">
                            {{ $carteassurance->assurance->nom }}
                        </a>
                    @else
                        Non spécifiée
                    @endif
                </p>

                <p><strong>Taux de couverture :</strong> {{ $carteassurance->taux_couverture }} %</p>

                <p><strong>Date de début de validité :</strong> {{ $carteassurance->date_debut ? $carteassurance->date_debut->format('d/m/Y') : 'Non renseignée' }}</p>

                <p><strong>Date de fin de validité :</strong> {{ $carteassurance->date_fin ? $carteassurance->date_fin->format('d/m/Y') : 'Non renseignée' }}</p>

                <p>
                    <strong>Statut :</strong>
                    @if ($carteassurance->statut)
                        <span class="badge bg-success">Actif</span>
                    @else
                        <span class="badge bg-danger">Inactif</span>
                    @endif
                </p>

                <p><strong>Date de création :</strong> {{ $carteassurance->created_at ? $carteassurance->created_at->format('d/m/Y H:i') : '-' }}</p>

                <p><strong>Dernière modification :</strong> {{ $carteassurance->updated_at ? $carteassurance->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div class="card-footer">
                {{-- Lien vers le formulaire de modification de cette carte d'assurance --}}
                <a href="{{ route('cartesassurances.edit', $carteassurance) }}" class="btn btn-warning">Modifier cette carte</a>
            </div>
        </div>
    </section>
@endsection

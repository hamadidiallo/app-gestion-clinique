@extends('layout')

{{-- Titre de la page d'affichage d'un patient --}}
@section('title', 'Détails du Patient')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails du Patient : {{ $patient->prenom }} {{ $patient->nom }}</h1>
            {{-- Bouton pour retourner à la liste des patients --}}
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Carte d'affichage des informations détaillées du patient --}}
        <div class="card mb-4">
            {{-- Entête du card avec l'ID du patient --}}
            <div class="card-header bg-primary text-white">
                Fiche Patient #{{ $patient->id }}
            </div>
            {{-- Corps du card contenant les détails --}}
            <div class="card-body">
                {{-- Prénom du patient --}}
                <p><strong>Prénom :</strong> {{ $patient->prenom }}</p>

                {{-- Nom du patient --}}
                <p><strong>Nom :</strong> {{ $patient->nom }}</p>

                {{-- Sexe du patient avec libellé explicite --}}
                <p><strong>Sexe :</strong> {{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>

                {{-- Numéro de téléphone du patient --}}
                <p><strong>Téléphone :</strong> {{ $patient->telephone ?? 'Non renseigné' }}</p>

                {{-- Statut d'assurance avec badge de couleur --}}
                <p>
                    <strong>Statut d'assurance :</strong>
                    @if ($patient->statut === 'assure')
                        <span class="badge bg-success">Assuré</span>
                    @else
                        <span class="badge bg-secondary">Non Assuré</span>
                    @endif
                </p>

                {{-- Date d'enregistrement du patient au format JJ/MM/AAAA HH:MM --}}
                <p><strong>Date de création :</strong> {{ $patient->created_at ? $patient->created_at->format('d/m/Y H:i') : '-' }}</p>

                {{-- Date de dernière mise à jour des informations --}}
                <p><strong>Dernière modification :</strong> {{ $patient->updated_at ? $patient->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            {{-- Pied du card avec bouton de modification --}}
            <div class="card-footer">
                {{-- Lien direct vers la page de modification du patient --}}
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">Modifier ce patient</a>
            </div>
        </div>
    </section>
@endsection

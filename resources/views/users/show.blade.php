@extends('layout')

{{-- Titre de la page d'affichage d'un utilisateur --}}
@section('title', 'Détails de l\'Utilisateur')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails de l'Utilisateur : {{ $user->prenom }} {{ $user->nom }}</h1>
            {{-- Bouton pour retourner à la liste des utilisateurs --}}
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Carte d'affichage des informations de l'utilisateur --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Fiche Utilisateur #{{ $user->id }}
            </div>
            <div class="card-body">
                <p><strong>Prénom :</strong> {{ $user->prenom }}</p>
                <p><strong>Nom :</strong> {{ $user->nom }}</p>
                <p><strong>Adresse Email :</strong> {{ $user->email }}</p>
                <p><strong>Rôle attribué :</strong> <span class="badge bg-info text-dark">{{ $user->role ? $user->role->nom : 'Aucun rôle' }}</span></p>
                <p><strong>Date de création :</strong> {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</p>
                <p><strong>Dernière modification :</strong> {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div class="card-footer">
                {{-- Lien vers le formulaire de modification de cet utilisateur --}}
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Modifier cet utilisateur</a>
            </div>
        </div>
    </section>
@endsection

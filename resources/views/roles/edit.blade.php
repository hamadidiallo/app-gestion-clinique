@extends('layout')

{{-- Titre de la page de modification d'un rôle --}}
@section('title', 'Modification du Rôle')

{{-- Contenu principal de la page --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier le Rôle : {{ $role->nom }}</h1>
            {{-- Bouton de retour vers la liste des rôles --}}
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de modification de rôle soumis en méthode PUT --}}
        <form action="{{ route('roles.update', $role) }}" method="post">
            {{-- Jeton de sécurité CSRF --}}
            @csrf

            {{-- Directive Blade pour simuler la méthode HTTP PUT --}}
            @method('PUT')

            {{-- Champ texte pour le nom du rôle --}}
            <x-form.input type="text" name="nom" label="Nom du rôle : " value="{{ old('nom', $role->nom) }}" />

            {{-- Champ texte pour la description du rôle --}}
            <x-form.input type="text" name="description" label="Description : " value="{{ old('description', $role->description) }}" />

            {{-- Bouton pour enregistrer les modifications --}}
            <button type="submit" class="btn btn-warning">Mettre à jour le rôle</button>
        </form>
    </section>
@endsection

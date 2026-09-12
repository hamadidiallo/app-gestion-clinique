@extends('layout')

{{-- Titre de la page de modification d'un utilisateur --}}
@section('title', 'Modification Utilisateur')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Modifier l'Utilisateur : {{ $user->prenom }} {{ $user->nom }}</h1>
            {{-- Bouton pour retourner à la liste des utilisateurs --}}
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de modification d'utilisateur soumis en méthode PUT --}}
        <form action="{{ route('users.update', $user) }}" method="post">
            {{-- Jeton de sécurité CSRF --}}
            @csrf

            {{-- Directive Blade pour simuler la méthode HTTP PUT --}}
            @method('PUT')

            {{-- Champ pour la modification du prénom --}}
            <x-form.input type="text" name="prenom" label="Prénom : " value="{{ old('prenom', $user->prenom) }}" />

            {{-- Champ pour la modification du nom --}}
            <x-form.input type="text" name="nom" label="Nom : " value="{{ old('nom', $user->nom) }}" />

            {{-- Champ pour la modification de l'adresse email --}}
            <x-form.input type="email" name="email" label="Adresse Email : " value="{{ old('email', $user->email) }}" />

            {{-- Champ facultatif pour changer le mot de passe --}}
            <x-form.input type="password" name="password" value="{{ old('password', $user->password) }}" label="Nouveau mot de passe (laisser vide pour conserver l'actuel) : " />

            {{-- Liste déroulante des rôles pré-sélectionnant le rôle actuel --}}
            <x-form.input type="select" name="role_id" label="Rôle : " :options="$roles" value="{{ old('role_id', $user->role_id) }}" />

            {{-- Bouton pour enregistrer les modifications --}}
            <button type="submit" class="btn btn-warning">Mettre à jour l'utilisateur</button>
        </form>
    </section>
@endsection

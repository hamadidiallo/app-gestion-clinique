@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Création Utilisateur')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page de création --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Création d'un Utilisateur</h1>
            {{-- Bouton pour retourner à la liste des utilisateurs --}}
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Formulaire de création d'utilisateur --}}
        <form action="{{ route('users.store') }}" method="post">
            {{-- Jeton CSRF pour la sécurité du formulaire --}}
            @csrf

            {{-- Affichage du résumé des erreurs de validation en haut du formulaire --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Champ pour la saisie du prénom --}}
            <x-form.input type="text" name="prenom" label="Prénom : " value="{{ old('prenom') }}" />

            {{-- Champ pour la saisie du nom --}}
            <x-form.input type="text" name="nom" label="Nom : " value="{{ old('nom') }}" />

            {{-- Champ pour la saisie de l'adresse email --}}
            <x-form.input type="email" name="email" label="Adresse Email : " value="{{ old('email') }}" />

            {{-- Champ pour la saisie du mot de passe --}}
            <x-form.input type="password" name="password" label="Mot de passe : " value="{{ old('password') }}" />

            {{-- Liste déroulante des rôles (nom du rôle affiché, id comme valeur) --}}
            <x-form.input type="select" name="role_id" label="Rôle : " :options="$roles" value="{{ old('role_id') }}" />

            {{-- Bouton de soumission du formulaire --}}
            <button type="submit" class="btn btn-outline-warning">Créer l'utilisateur</button>
        </form>
    </section>
@endsection

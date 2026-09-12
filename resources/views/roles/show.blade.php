@extends('layout')

{{-- Titre de la page d'affichage d'un rôle --}}
@section('title', 'Détails du Rôle')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails du Rôle : {{ $role->nom }}</h1>
            {{-- Bouton pour retourner à la liste des rôles --}}
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Carte d'affichage des informations du rôle --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Informations du rôle #{{ $role->id }}
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> {{ $role->nom }}</p>
                <p><strong>Description :</strong> {{ $role->description ?: 'Aucune description disponible' }}</p>
                <p><strong>Date de création :</strong> {{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : '-' }}</p>
                <p><strong>Dernière modification :</strong> {{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div class="card-footer">
                {{-- Lien vers le formulaire de modification du rôle --}}
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">Modifier ce rôle</a>
            </div>
        </div>

        {{-- Liste des utilisateurs possédant ce rôle --}}
        <h3>Utilisateurs associés ({{ $role->users->count() }})</h3>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($role->users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->prenom }}</td>
                        <td>{{ $user->nom }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucun utilisateur n'est attribué à ce rôle.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

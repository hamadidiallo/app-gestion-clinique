@extends('layout')

{{-- Titre de la page dans l'onglet du navigateur --}}
@section('title', 'Liste des Utilisateurs')

{{-- Contenu principal de la page --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton de création d'utilisateur --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1>Liste des Utilisateurs</h1>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="usersTable" title="Liste des Utilisateurs" filename="utilisateurs" />
                <a href="{{ route('user.create') }}" class="btn btn-primary">Créer un utilisateur</a>
            </div>
        </div>

        {{-- Tableau Bootstrap pour l'affichage de la liste des utilisateurs --}}
        <table id="usersTable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Prénom</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Rôle</th>
                    <th scope="col">Date de création</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Parcours de la liste des utilisateurs passée depuis le contrôleur --}}
                @forelse($users as $user)
                    <tr>
                        {{-- Identifiant de l'utilisateur --}}
                        <th scope="row">{{ $user->id }}</th>
                        {{-- Prénom de l'utilisateur --}}
                        <td>{{ $user->prenom }}</td>
                        {{-- Nom de l'utilisateur --}}
                        <td>{{ $user->nom }}</td>
                        {{-- Email de l'utilisateur --}}
                        <td>{{ $user->email }}</td>
                        {{-- Nom du rôle associé à l'utilisateur --}}
                        <td>{{ $user->role ? $user->role->nom : '-' }}</td>
                        {{-- Date de création au format Jour/Mois/Année Heure:Minute --}}
                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</td>
                        {{-- Colonne des boutons d'actions (Voir, Modifier, Supprimer via Modal Bootstrap) --}}
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Bouton pour consulter la fiche de l'utilisateur --}}
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info text-white" title="Voir">
                                    Voir
                                </a>

                                {{-- Bouton pour éditer les informations de l'utilisateur --}}
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    Modifier
                                </a>

                                {{-- Bouton déclenchant l'ouverture de la fenêtre modale Bootstrap de suppression --}}
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}" title="Supprimer">
                                    Supprimer
                                </button>
                            </div>

                            {{-- Modal Bootstrap professionnel de confirmation de suppression --}}
                            <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        {{-- En-tête de la fenêtre modale --}}
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">
                                                Confirmation de suppression
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>

                                        {{-- Corps de la fenêtre modale --}}
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->prenom }} {{ $user->nom }}</strong> ?
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <small>Cette opération est irréversible et supprimera définitivement le compte utilisateur.</small>
                                            </div>
                                        </div>

                                        {{-- Pied de la fenêtre modale avec boutons Annuler et Confirmer --}}
                                        <div class="modal-footer">
                                            {{-- Bouton Annuler qui ferme la fenêtre sans exécuter la suppression --}}
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                                            {{-- Formulaire effectuant la requête HTTP DELETE en cas de confirmation --}}
                                            <form action="{{ route('users.destroy', $user) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Message affiché si la liste des utilisateurs est vide --}}
                    <tr>
                        <td colspan="7" class="text-center">Aucun utilisateur trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

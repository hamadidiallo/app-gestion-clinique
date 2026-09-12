@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Liste des Rôles')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la vue : titre et bouton vers le formulaire de création --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1>Liste des rôles</h1>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="rolesTable" title="Liste des Rôles" filename="roles" />
                <a href="{{ route('role.create') }}" class="btn btn-primary">Créer un rôle</a>
            </div>
        </div>

        {{-- Tableau Bootstrap affichant la liste des rôles --}}
        <table id="rolesTable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Description</th>
                    <th scope="col">Date de création</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Parcourt la liste des rôles transmise par le contrôleur --}}
                @forelse($roles as $role)
                    <tr>
                        <th scope="row">{{ $role->id }}</th>
                        <td>{{ $role->nom }}</td>
                        <td>{{ $role->description ?: '-' }}</td>
                        {{-- Affichage de la date de création formatée (ex: 03/09/2026 14:30) --}}
                        <td>{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : '-' }}</td>
                        {{-- Colonne des boutons d'actions (Voir, Modifier, Supprimer via Modal Bootstrap) --}}
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Bouton pour afficher les détails du rôle --}}
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-info text-white" title="Voir">
                                    Voir
                                </a>

                                {{-- Bouton pour accéder au formulaire de modification du rôle --}}
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    Modifier
                                </a>

                                {{-- Bouton déclenchant l'ouverture de la fenêtre modale Bootstrap de suppression --}}
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}" title="Supprimer">
                                    Supprimer
                                </button>
                            </div>

                            {{-- Modal Bootstrap professionnel de confirmation de suppression --}}
                            <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content text-start">
                                        {{-- En-tête de la fenêtre modale --}}
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteRoleModalLabel{{ $role->id }}">
                                                Confirmation de suppression
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>

                                        {{-- Corps de la fenêtre modale --}}
                                        <div class="modal-body">
                                            Êtes-vous sûr de vouloir supprimer le rôle <strong>{{ $role->nom }}</strong> ?
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <small>Attention : Cette action est irréversible et retirera ce rôle de l'application.</small>
                                            </div>
                                        </div>

                                        {{-- Pied de la fenêtre modale avec boutons Annuler et Confirmer --}}
                                        <div class="modal-footer">
                                            {{-- Bouton Annuler fermant la modale --}}
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                                            {{-- Formulaire effectuant la suppression HTTP DELETE --}}
                                            <form action="{{ route('roles.destroy', $role) }}" method="post" class="d-inline">
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
                    {{-- Message affiché si aucun rôle n'existe dans la base de données --}}
                    <tr>
                        <td colspan="5" class="text-center">Aucun rôle trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

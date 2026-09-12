@extends('layout')

@section('title', 'Catégories de Dépenses')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Catégories de Charges & Dépenses</h1>
            <a href="{{ route('categoriedepenses.create') }}" class="btn btn-primary">Nouvelle Catégorie</a>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Code</th>
                    <th scope="col">Intitulé</th>
                    <th scope="col">Description</th>
                    <th scope="col">Statut</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorieDepenses as $cat)
                    <tr>
                        <th scope="row">{{ $cat->id }}</th>
                        <td><code>{{ $cat->code }}</code></td>
                        <td><strong>{{ $cat->nom }}</strong></td>
                        <td>{{ $cat->description ?? '-' }}</td>
                        <td>
                            @if ($cat->statut)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('categoriedepenses.show', $cat) }}" class="btn btn-sm btn-info text-white">Voir</a>
                                <a href="{{ route('categoriedepenses.edit', $cat) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCatModal{{ $cat->id }}">Supprimer</button>
                            </div>

                            <div class="modal fade" id="deleteCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmation de suppression</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $cat->nom }}</strong> ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form action="{{ route('categoriedepenses.destroy', $cat) }}" method="post" class="d-inline">
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
                    <tr>
                        <td colspan="6" class="text-center">Aucune catégorie de dépense répertoriée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

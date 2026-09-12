@extends('layout')

{{-- Titre de la page dans l'onglet du navigateur --}}
@section('title', 'Liste des Employés')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec titre et bouton d'ajout --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1>Liste des Employés de la Clinique</h1>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="employesTable" title="Répertoire des Employés" filename="employes" />
                <a href="{{ route('employes.create') }}" class="btn btn-primary">Ajouter un employé</a>
            </div>
        </div>

        {{-- Tableau récapitulatif des employés --}}
        <table id="employesTable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nom & Prénom</th>
                    <th scope="col">Fonction</th>
                    <th scope="col">Téléphone</th>
                    <th scope="col">Rémunération</th>
                    <th scope="col">Date Embauche</th>
                    <th scope="col">Statut</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Boucle d'affichage des employés --}}
                @forelse($employes as $employe)
                    <tr>
                        <th scope="row">{{ $employe->id }}</th>
                        <td><strong>{{ $employe->prenom }} {{ $employe->nom }}</strong></td>
                        <td>{{ $employe->fonction }}</td>
                        <td>{{ $employe->telephone }}</td>
                        <td>{{ number_format($employe->salaire_fixe, 2, ',', ' ') }} FCFA</td>
                        <td>{{ $employe->date_embauche ? $employe->date_embauche->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if ($employe->statut)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('employes.show', $employe) }}" class="btn btn-sm btn-info text-white">Voir</a>
                                <a href="{{ route('employes.edit', $employe) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeModal{{ $employe->id }}">Supprimer</button>
                            </div>

                            {{-- Modale de confirmation de suppression --}}
                            <div class="modal fade" id="deleteEmployeModal{{ $employe->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmation de suppression</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir supprimer l'employé <strong>{{ $employe->prenom }} {{ $employe->nom }}</strong> ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form action="{{ route('employes.destroy', $employe) }}" method="post" class="d-inline">
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
                        <td colspan="8" class="text-center">Aucun employé enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

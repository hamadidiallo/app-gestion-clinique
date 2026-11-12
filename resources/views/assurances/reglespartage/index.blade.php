@extends('layout')

@section('title', 'Règles de Partage')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Règles de Partage & Rétrocession Médicale</h1>
            <a href="{{ route('reglespartage.create') }}" class="btn btn-primary">Créer une Règle</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Service Médical</th>
                    <th scope="col">Part Médecin (%)</th>
                    <th scope="col">Part Clinique (%)</th>
                    <th scope="col">Période d'effet</th>
                    <th scope="col">Statut</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reglesPartage as $regle)
                    <tr>
                        <th scope="row">{{ $regle->id }}</th>
                        <td><strong>{{ $regle->service ? $regle->service->nom : 'N/A' }}</strong></td>
                        <td><span class="badge bg-primary fs-6">{{ $regle->pourcentage_medecin }} %</span></td>
                        <td><span class="badge bg-info text-dark fs-6">{{ $regle->pourcentage_clinique }} %</span></td>
                        <td>
                            Du {{ $regle->date_debut ? $regle->date_debut->format('d/m/Y') : '...' }}
                            au {{ $regle->date_fin ? $regle->date_fin->format('d/m/Y') : 'Permanent' }}
                        </td>
                        <td>
                            @if ($regle->statut)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('reglespartage.show', $regle) }}" class="btn btn-sm btn-info text-white">Voir</a>
                                <a href="{{ route('reglespartage.edit', $regle) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRegleModal{{ $regle->id }}">Supprimer</button>
                            </div>

                            <div class="modal fade" id="deleteRegleModal{{ $regle->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmation de suppression</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir supprimer la règle de partage pour le service <strong>{{ $regle->service->nom ?? '' }}</strong> ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form action="{{ route('reglespartage.destroy', $regle) }}" method="post" class="d-inline">
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
                        <td colspan="7" class="text-center">Aucune règle de partage configurée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </section>
@endsection

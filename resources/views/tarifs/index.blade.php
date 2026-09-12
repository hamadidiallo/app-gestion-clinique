@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Liste des Tarifs')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête avec le titre de la liste et le bouton de création d'un tarif --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1>Liste des Tarifs de la Clinique</h1>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="tarifsTable" title="Grille Tarifaire" filename="tarifs" />
                <a href="{{ route('tarif.create') }}" class="btn btn-primary">Créer un tarif</a>
            </div>
        </div>

        {{-- Tableau des tarifs de la clinique --}}
        <table id="tarifsTable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Service Médical</th>
                    <th scope="col">Tarif Normal</th>
                    <th scope="col">Tarif AMO</th>
                    <th scope="col">Tarif Spécifique</th>
                    <th scope="col">Période Validité</th>
                    <th scope="col">Statut</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Boucle d'affichage de l'ensemble des tarifs transmises par le contrôleur --}}
                @forelse($tarifs as $tarif)
                    <tr>
                        {{-- Identifiant du tarif --}}
                        <th scope="row">{{ $tarif->id }}</th>

                        {{-- Nom du service rattaché au tarif --}}
                        <td>
                            @if($tarif->service)
                                <strong>{{ $tarif->service->nom }}</strong>
                            @else
                                <span class="text-muted">Non attribué</span>
                            @endif
                        </td>

                        {{-- Montant du tarif normal formaté avec 2 décimales --}}
                        <td>{{ number_format($tarif->tarif_normal, 2, ',', ' ') }} FCFA</td>

                        {{-- Montant du tarif AMO s'il existe --}}
                        <td>{{ $tarif->tarif_amo ? number_format($tarif->tarif_amo, 2, ',', ' ') . ' FCFA' : '-' }}</td>

                        {{-- Montant du tarif spécifique s'il existe --}}
                        <td>{{ $tarif->tarif_specifique ? number_format($tarif->tarif_specifique, 2, ',', ' ') . ' FCFA' : '-' }}</td>

                        {{-- Plage de validité du tarif ou statut Permanent --}}
                        <td>
                            @if($tarif->date_debut || $tarif->date_fin)
                                {{ $tarif->date_debut ? $tarif->date_debut->format('d/m/Y') : '...' }} au {{ $tarif->date_fin ? $tarif->date_fin->format('d/m/Y') : '...' }}
                            @else
                                Permanent
                            @endif
                        </td>

                        {{-- Indication visuelle du statut du tarif (Actif/Inactif) --}}
                        <td>
                            @if ($tarif->statut)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>

                        {{-- Boutons pour exécuter les actions sur chaque tarif --}}
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Bouton de consultation des détails du tarif --}}
                                <a href="{{ route('tarifs.show', $tarif) }}" class="btn btn-sm btn-info text-white" title="Voir">
                                    Voir
                                </a>

                                {{-- Bouton d'édition des valeurs du tarif --}}
                                <a href="{{ route('tarifs.edit', $tarif) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    Modifier
                                </a>

                                {{-- Bouton pour ouvrir la modale de confirmation de suppression --}}
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTarifModal{{ $tarif->id }}" title="Supprimer">
                                    Supprimer
                                </button>
                            </div>

                            {{-- Window Modal de confirmation de suppression --}}
                            <div class="modal fade" id="deleteTarifModal{{ $tarif->id }}" tabindex="-1" aria-labelledby="deleteTarifModalLabel{{ $tarif->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        {{-- En-tête de la modale --}}
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteTarifModalLabel{{ $tarif->id }}">
                                                Confirmation de suppression
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>

                                        {{-- Contenu textuel de la modale --}}
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir supprimer le tarif pour <strong>{{ $tarif->service->nom ?? 'ce service' }}</strong> ?
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <small>Cette action retirera cette ligne tarifaire de la base de données.</small>
                                            </div>
                                        </div>

                                        {{-- Pied de modale avec boutons d'action --}}
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                                            {{-- Formulaire effectuant la suppression par la méthode DELETE --}}
                                            <form action="{{ route('tarifs.destroy', $tarif) }}" method="post" class="d-inline">
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
                    {{-- Message si la liste des tarifs est vide --}}
                    <tr>
                        <td colspan="8" class="text-center">Aucun tarif disponible dans la base de données.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

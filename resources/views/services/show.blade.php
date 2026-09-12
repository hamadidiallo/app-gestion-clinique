@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Détails du Service Médical')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la vue détaillée avec le nom du service et les boutons d'action --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails du Service : {{ $service->nom }}</h1>
            <div class="gap-2 d-flex">
                {{-- Bouton pour éditer le service actuel --}}
                <a href="{{ route('services.edit', $service) }}" class="btn btn-warning">Modifier</a>
                {{-- Bouton de retour à la liste générale des services --}}
                <a href="{{ route('services.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        {{-- Carte d'affichage des informations détaillées du service --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Nom du Service :</div>
                    <div class="col-md-9">{{ $service->nom }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Code Identifiant :</div>
                    <div class="col-md-9"><code>{{ $service->code }}</code></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Statut :</div>
                    <div class="col-md-9">
                        @if ($service->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Description :</div>
                    <div class="col-md-9">{{ $service->description ?? 'Aucune description fournie.' }}</div>
                </div>
                <div class="row">
                    <div class="col-md-3 fw-bold">Date de création :</div>
                    <div class="col-md-9">{{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Tableau récapitulatif des tarifs associés à ce service --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">Grille des Tarifs Associés ({{ $service->tarifs->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tarif Normal</th>
                            <th>Tarif AMO</th>
                            <th>Tarif Spécifique</th>
                            <th>Période de Validité</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($service->tarifs as $tarif)
                            <tr>
                                <td>{{ number_format($tarif->tarif_normal, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $tarif->tarif_amo ? number_format($tarif->tarif_amo, 2, ',', ' ') . ' FCFA' : '-' }}</td>
                                <td>{{ $tarif->tarif_specifique ? number_format($tarif->tarif_specifique, 2, ',', ' ') . ' FCFA' : '-' }}</td>
                                <td>
                                    @if($tarif->date_debut || $tarif->date_fin)
                                        {{ $tarif->date_debut ? $tarif->date_debut->format('d/m/Y') : '...' }} au {{ $tarif->date_fin ? $tarif->date_fin->format('d/m/Y') : '...' }}
                                    @else
                                        Permanent
                                    @endif
                                </td>
                                <td>
                                    @if ($tarif->statut)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('tarifs.show', $tarif) }}" class="btn btn-sm btn-info text-white">Voir Tarif</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">Aucun tarif associé à ce service pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

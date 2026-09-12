@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Détails du Tarif')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la vue détaillée avec identifiant du tarif et boutons d'action --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Fiche Tarifaire #{{ $tarif->id }}</h1>
            <div class="gap-2 d-flex">
                {{-- Bouton pour éditer le tarif courant --}}
                <a href="{{ route('tarifs.edit', $tarif) }}" class="btn btn-warning">Modifier</a>
                {{-- Bouton pour revenir à la liste complète des tarifs --}}
                <a href="{{ route('tarifs.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        {{-- Carte d'affichage des détails du tarif --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Financières & Service</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Service Médical Associé :</div>
                    <div class="col-md-9">
                        @if($tarif->service)
                            <a href="{{ route('services.show', $tarif->service) }}" class="fw-semibold text-decoration-none">
                                {{ $tarif->service->nom }} (Code : {{ $tarif->service->code }})
                            </a>
                        @else
                            <span class="text-muted">Aucun service associé</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Tarif Normal :</div>
                    <div class="col-md-9 fs-5 text-success fw-bold">
                        {{ number_format($tarif->tarif_normal, 2, ',', ' ') }} FCFA
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Tarif AMO (Assurance) :</div>
                    <div class="col-md-9">
                        {{ $tarif->tarif_amo ? number_format($tarif->tarif_amo, 2, ',', ' ') . ' FCFA' : 'Non renseigné' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Tarif Spécifique :</div>
                    <div class="col-md-9">
                        {{ $tarif->tarif_specifique ? number_format($tarif->tarif_specifique, 2, ',', ' ') . ' FCFA' : 'Non renseigné' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Période de Validité :</div>
                    <div class="col-md-9">
                        @if($tarif->date_debut || $tarif->date_fin)
                            Du <strong>{{ $tarif->date_debut ? $tarif->date_debut->format('d/m/Y') : 'Non définie' }}</strong>
                            au <strong>{{ $tarif->date_fin ? $tarif->date_fin->format('d/m/Y') : 'Indéterminée' }}</strong>
                        @else
                            <span class="badge bg-info text-dark">Permanent</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Statut du Tarif :</div>
                    <div class="col-md-9">
                        @if ($tarif->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Description / Observations :</div>
                    <div class="col-md-9">{{ $tarif->description ?? 'Aucune observation enregistrée.' }}</div>
                </div>

                <div class="row">
                    <div class="col-md-3 fw-bold">Date de Création :</div>
                    <div class="col-md-9">{{ $tarif->created_at ? $tarif->created_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection

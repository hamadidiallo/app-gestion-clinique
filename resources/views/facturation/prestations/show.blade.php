@extends('layout')

{{-- Titre de la page affiché dans l'onglet du navigateur --}}
@section('title', 'Détails de la Prestation')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la vue détaillée avec l'identifiant de la prestation et les boutons d'action --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Prestation Médicale #{{ $prestation->id }}</h1>
            <div class="gap-2 d-flex">
                {{-- Bouton pour éditer la prestation courante --}}
                <a href="{{ route('prestations.edit', $prestation) }}" class="btn btn-warning">Modifier</a>
                {{-- Bouton pour revenir à la liste générale des prestations --}}
                <a href="{{ route('prestations.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        {{-- Carte d'affichage des détails complets de la prestation --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Générales de la Prestation</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Patient Bénéficiaire :</div>
                    <div class="col-md-9">
                        @if($prestation->patient)
                            <a href="{{ route('patients.show', $prestation->patient) }}" class="fw-semibold text-decoration-none">
                                {{ $prestation->patient->prenom }} {{ $prestation->patient->nom }} (Tél: {{ $prestation->patient->telephone ?? 'N/A' }})
                            </a>
                        @else
                            <span class="text-muted">Information non disponible</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Service Médical :</div>
                    <div class="col-md-9">
                        @if($prestation->service)
                            <a href="{{ route('services.show', $prestation->service) }}" class="fw-semibold text-decoration-none">
                                {{ $prestation->service->nom }} (Code: {{ $prestation->service->code }})
                            </a>
                        @else
                            <span class="text-muted">Non attribué</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Médecin Traitant :</div>
                    <div class="col-md-9">
                        @if($prestation->medecin)
                            <strong>Dr. {{ $prestation->medecin->prenom }} {{ $prestation->medecin->nom }}</strong>
                            {{ $prestation->medecin->specialite ? ' (' . $prestation->medecin->specialite . ')' : '' }}
                        @else
                            <span class="text-muted">Aucun médecin affecté</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Acte / Nomenclature :</div>
                    <div class="col-md-9">
                        @if($prestation->acte)
                            <a href="{{ route('actes.show', $prestation->acte) }}" class="text-decoration-none fw-semibold text-teal">
                                [{{ $prestation->acte->code }}] {{ $prestation->acte->nom }} ({{ number_format($prestation->acte->tarif_base, 0, ',', ' ') }} FCFA)
                            </a>
                        @else
                            <span class="text-muted">Aucun acte spécifique lié</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Type / Catégorie :</div>
                    <div class="col-md-9">{{ $prestation->type ?? 'Non spécifié' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Montant Facturé :</div>
                    <div class="col-md-9 fs-5 text-success fw-bold">
                        {{ number_format($prestation->montant, 2, ',', ' ') }} FCFA
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Date & Heure de Réalisation :</div>
                    <div class="col-md-9">
                        {{ $prestation->date_prestation ? $prestation->date_prestation->format('d/m/Y à H:i') : '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Statut de la Prestation :</div>
                    <div class="col-md-9">
                        @if ($prestation->statut)
                            <span class="badge bg-success fs-6">Effectuée / Active</span>
                        @else
                            <span class="badge bg-danger fs-6">Annulée / Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Description & Compte-rendu :</div>
                    <div class="col-md-9">{{ $prestation->description ?? 'Aucun compte-rendu saisi.' }}</div>
                </div>

                <div class="row">
                    <div class="col-md-3 fw-bold">Date d'enregistrement :</div>
                    <div class="col-md-9">{{ $prestation->created_at ? $prestation->created_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layout')

{{-- Titre de la page d'affichage d'une assurance --}}
@section('title', 'Détails de l\'Assurance')

{{-- Contenu principal de la vue --}}
@section('content')
    <section class="mt-4">
        {{-- En-tête de la page avec titre et bouton de retour --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails de la Compagnie : {{ $assurance->nom }}</h1>
            {{-- Bouton pour retourner à la liste des assurances --}}
            <a href="{{ route('assurances.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        {{-- Carte d'affichage des informations de l'assurance --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Fiche Assurance #{{ $assurance->id }}
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> {{ $assurance->nom }}</p>
                <p><strong>Code identifiant :</strong> {{ $assurance->code ?? 'Non défini' }}</p>
                <p><strong>Téléphone :</strong> {{ $assurance->telephone ?? 'Non renseigné' }}</p>
                <p><strong>Adresse Email :</strong> {{ $assurance->email ?? 'Non renseignée' }}</p>
                <p><strong>Adresse physique :</strong> {{ $assurance->adresse ?? 'Non renseignée' }}</p>
                <p>
                    <strong>Statut :</strong>
                    @if ($assurance->statut)
                        <span class="badge bg-success">Actif</span>
                    @else
                        <span class="badge bg-danger">Inactif</span>
                    @endif
                </p>
                <p><strong>Date de création :</strong> {{ $assurance->created_at ? $assurance->created_at->format('d/m/Y H:i') : '-' }}</p>
                <p><strong>Dernière modification :</strong> {{ $assurance->updated_at ? $assurance->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div class="card-footer">
                {{-- Lien vers le formulaire de modification de cette assurance --}}
                <a href="{{ route('assurances.edit', $assurance) }}" class="btn btn-warning">Modifier cette assurance</a>
            </div>
        </div>

        {{-- Section affichant la liste des cartes d'assurance rattachées à cette compagnie --}}
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Cartes d'assurance associées ({{ $assurance->cartesAssurance->count() }})
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Référence</th>
                            <th>Patient</th>
                            <th>Taux de couverture</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assurance->cartesAssurance as $carte)
                            <tr>
                                <td>{{ $carte->id }}</td>
                                <td>{{ $carte->reference }}</td>
                                <td>{{ $carte->patient ? $carte->patient->prenom . ' ' . $carte->patient->nom : '-' }}</td>
                                <td>{{ $carte->taux_couverture }} %</td>
                                <td>
                                    @if ($carte->statut)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune carte d'assurance associée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@extends('layout')

@section('title', 'Liste des Médecins - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête avec le titre de la liste et le bouton de création d'un médecin --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="user-check" class="me-2"></i>Corps Médical de la Clinique
                </h1>
                <p class="text-muted mb-0">Gestion du répertoire des praticiens et suivi de leur activité médicale.</p>
            </div>
            <div>
                <a href="{{ route('medecins.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Ajouter un Médecin
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau récapitulatif des médecins enregistrés --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="list-check" class="text-primary me-2"></i>Répertoire des Médecins
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="medecinsTable" title="Répertoire des Médecins" filename="medecins" />
                    <span class="badge bg-light text-dark border fs-7">{{ $medecins->count() }} médecin(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="medecinsTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Nom & Prénom</th>
                                <th>Spécialité</th>
                                <th>Téléphone</th>
                                <th class="text-end">Actes (Période)</th>
                                <th class="text-end">Honoraires Estimés</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medecins as $medecin)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $medecin->id }}</th>
                                    <td><strong>Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</strong></td>
                                    <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $medecin->specialite }}</span></td>
                                    <td>{{ $medecin->telephone ?? '-' }}</td>
                                    <td class="text-end fw-semibold text-primary">
                                        {{ number_format($medecin->prestations_count ?? 0, 0, ',', ' ') }} acte(s)
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($medecin->prestations_sum_part_medecin ?? 0, 0, ',', ' ') }} FBU
                                    </td>
                                    <td class="text-center">
                                        @if ($medecin->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Actif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('medecins.show', $medecin) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('medecins.edit', $medecin) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMedecinModal{{ $medecin->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modale de confirmation de suppression --}}
                                        <div class="modal fade" id="deleteMedecinModal{{ $medecin->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer la fiche du <strong>Dr. {{ $medecin->prenom }} {{ $medecin->nom }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('medecins.destroy', $medecin) }}" method="post" class="d-inline">
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
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i data-lucide="user-check" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucun médecin enregistré dans la base de données.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

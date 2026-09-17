@extends('layout')

@section('title', 'Liste des Prestations Médicales - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête de la page avec le titre et le bouton de création d'une prestation --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="file-text" class="me-2"></i>Liste des Prestations Médicales
                </h1>
                <p class="text-muted mb-0">Gestion et suivi des soins, examens et actes dispensés aux patients.</p>
            </div>
            <div>
                <a href="{{ route('prestation.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Enregistrer une Prestation
                </a>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Cartes de synthèse --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                            <i data-lucide="journal-check" class="fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Prestations Médicales (Période)</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($prestations->where('statut', true)->sum('montant'), 0, ',', ' ') }} FBU</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i data-lucide="activity" class="fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Nombre d'Actes Réalisés</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ $prestations->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des prestations enregistrées dans la clinique --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="list-check" class="text-primary me-2"></i>Registre des Actes Soignants
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="prestationsTable" title="Registre des Actes Soignants" filename="prestations" />
                    <span class="badge bg-light text-dark border fs-7">{{ $prestations->count() }} acte(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="prestationsTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Patient</th>
                                <th>Service Médical</th>
                                <th>Médecin Traitant</th>
                                <th>Date & Heure</th>
                                <th>Type</th>
                                <th class="text-end">Montant</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prestations as $prestation)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $prestation->id }}</th>
                                    <td>
                                        @if($prestation->patient)
                                            <strong>{{ $prestation->patient->prenom }} {{ $prestation->patient->nom }}</strong>
                                        @else
                                            <span class="text-muted">Inconnu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prestation->service)
                                            <span class="badge bg-primary-subtle text-primary border">{{ $prestation->service->nom }}</span>
                                        @else
                                            <span class="text-muted">Non défini</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prestation->medecin)
                                            Dr. {{ $prestation->medecin->prenom }} {{ $prestation->medecin->nom }}
                                        @else
                                            <span class="text-muted">Non attribué</span>
                                        @endif
                                    </td>
                                    <td>{{ $prestation->date_prestation ? \Carbon\Carbon::parse($prestation->date_prestation)->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $prestation->type ?? 'Général' }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($prestation->montant, 0, ',', ' ') }} FBU</td>
                                    <td class="text-center">
                                        @if ($prestation->statut)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Effectuée</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Annulée</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('prestations.show', $prestation) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('prestations.edit', $prestation) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePrestationModal{{ $prestation->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modal Bootstrap de confirmation de suppression --}}
                                        <div class="modal fade" id="deletePrestationModal{{ $prestation->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer la prestation #<strong>{{ $prestation->id }}</strong> pour le patient <strong>{{ $prestation->patient->nom ?? '' }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('prestations.destroy', $prestation) }}" method="post" class="d-inline">
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
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i data-lucide="file-text" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune prestation médicale enregistrée pour cette période.
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

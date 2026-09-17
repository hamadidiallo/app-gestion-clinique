@extends('layout')

@section('title', 'Rémunérations Médicales - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête du module --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="calculator" class="me-2"></i>Rémunérations & Rétrocessions Médecins
                </h1>
                <p class="text-muted mb-0">Calcul des honoraires et paie des praticiens médicaux.</p>
            </div>
            <div>
                <a href="{{ route('remunerations.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle"></i> Générer une Paie
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
                            <i data-lucide="banknote" class="fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Total Rétrocession Médecins (Période)</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($remunerations->sum('montant_medecin'), 0, ',', ' ') }} FBU</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i data-lucide="building-check" class="fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Part Conservée Clinique (Période)</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ number_format($remunerations->sum('montant_clinique'), 0, ',', ' ') }} FBU</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des rémunérations --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="file-earmark-spreadsheet" class="text-primary me-2"></i>Registre des Paies
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="remunerationsTable" title="Registre des Paies & Rétrocessions Médecins" filename="remunerations_medecins" />
                    <span class="badge bg-light text-dark border fs-7">{{ $remunerations->count() }} fiche(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="remunerationsTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Médecin</th>
                                <th>Période Décompte</th>
                                <th>Type Rémunération</th>
                                <th class="text-end">Base Générée</th>
                                <th class="text-end">Part Médecin</th>
                                <th class="text-end">Part Clinique</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remunerations as $remun)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $remun->id }}</th>
                                    <td><strong>Dr. {{ $remun->medecin ? $remun->medecin->prenom . ' ' . $remun->medecin->nom : 'N/A' }}</strong></td>
                                    <td>{{ $remun->periode_debut ? \Carbon\Carbon::parse($remun->periode_debut)->format('d/m/Y') : '' }} au {{ $remun->periode_fin ? \Carbon\Carbon::parse($remun->periode_fin)->format('d/m/Y') : '' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-dark border">{{ ucfirst($remun->type_remuneration ?? 'Acte') }}</span></td>
                                    <td class="text-end fw-semibold text-dark">{{ number_format($remun->montant_base, 0, ',', ' ') }} FBU</td>
                                    <td class="text-end"><strong class="text-success">{{ number_format($remun->montant_medecin, 0, ',', ' ') }} FBU</strong></td>
                                    <td class="text-end text-primary fw-semibold">{{ number_format($remun->montant_clinique, 0, ',', ' ') }} FBU</td>
                                    <td class="text-center">
                                        @if ($remun->statut == 'payee' || $remun->statut == 'paye')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Payé</span>
                                        @elseif ($remun->statut == 'en_attente' || $remun->statut == 'calculee')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">En attente</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Annulé</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('remunerations.show', $remun) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i data-lucide="eye"></i>
                                            </a>
                                            <a href="{{ route('remunerations.edit', $remun) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i data-lucide="edit-3"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRemunModal{{ $remun->id }}" title="Supprimer">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>

                                        {{-- Modal de suppression --}}
                                        <div class="modal fade" id="deleteRemunModal{{ $remun->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i data-lucide="alert-triangle" class="me-2"></i>Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer cette fiche de rémunération ?
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('remunerations.destroy', $remun) }}" method="post" class="d-inline">
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
                                        <i data-lucide="calculator" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune rémunération générée pour cette période.
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

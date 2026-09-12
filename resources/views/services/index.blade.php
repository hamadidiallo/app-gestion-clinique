@extends('layout')

@section('title', 'Gestion des Services Médicaux - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-building me-2"></i>Services Médicaux & Départements
            </h1>
            <p class="text-muted mb-0">Gestion de la structure médicale, départements et grilles tarifaires associées.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('service.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Créer un Service
            </a>
            <a href="{{ route('tarif.create') }}" class="btn btn-outline-info fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-tags"></i> Ajouter un Tarif
            </a>
        </div>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-hospital fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Services Médicaux</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $services->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Services Actifs</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $services->where('statut', true)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3">
                        <i class="bi bi-tags fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Tarifs Rattachés</span>
                        <h4 class="fw-bold mb-0 text-info">{{ $services->sum('tarifs_count') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des services --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-task text-primary me-2"></i>Répertoire des Services Médicaux
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="servicesTable" title="Répertoire des Services Médicaux" filename="services_medicaux" />
                <span class="badge bg-light text-dark border fs-7">{{ $services->count() }} service(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="servicesTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>#</th>
                            <th><i class="bi bi-building text-muted me-1"></i>Nom du Service</th>
                            <th><i class="bi bi-barcode text-muted me-1"></i>Code</th>
                            <th><i class="bi bi-card-text text-muted me-1"></i>Description</th>
                            <th><i class="bi bi-tags text-muted me-1"></i>Tarifs Associés</th>
                            <th class="text-center"><i class="bi bi-flag text-muted me-1"></i>Statut</th>
                            <th><i class="bi bi-calendar-event text-muted me-1"></i>Date Création</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-secondary font-monospace">#{{ $service->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $service->nom }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-dark font-monospace">{{ $service->code }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ Str::limit($service->description ?? 'Aucune description', 50) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-tag-fill me-1"></i> {{ $service->tarifs_count ?? 0 }} tarif(s)
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($service->statut)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Actif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-dark fw-semibold">
                                        {{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-outline-primary" title="Voir Fiche">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir supprimer le service <strong>{{ $service->nom }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('services.destroy', $service) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2 text-secondary"></i>
                                    Aucun service médical répertorié. Cliquez sur "Créer un Service" pour commencer.
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

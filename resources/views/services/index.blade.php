@extends('layout')

@section('title', 'Gestion des Services Médicaux - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="building-2" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Services Médicaux & Départements</span>
            </h1>
            <p class="text-muted mb-0 small">Gestion de la structure médicale, départements et grilles tarifaires associées.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('service.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Créer un Service</span>
            </a>
            <a href="{{ route('actes.index') }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="file-text" style="width: 1rem; height: 1rem;"></i>
                <span>Catalogue des Actes</span>
            </a>
        </div>
    </div>

    {{-- Cartes de synthèse inspirées de Clinique.dc.html --}}
    <div class="row g-3 mb-4">
        {{-- Total Services --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="building-2" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-info-pill">Structure</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $services->count() }}
                </div>
                <div class="text-muted small">Total Services Médicaux</div>
            </div>
        </div>

        {{-- Services Actifs --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Opérationnels</span>
                </div>
                <div class="font-mono fw-bold text-success mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $services->where('statut', true)->count() }}
                </div>
                <div class="text-muted small">Services Actifs en Clinique</div>
            </div>
        </div>

        {{-- Total Actes --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="file-text" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-warning-pill">Nomenclature</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $services->sum('actes_count') }}
                </div>
                <div class="text-muted small">Total Actes Rattachés</div>
            </div>
        </div>
    </div>

    {{-- Tableau de données des services --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Répertoire des Services Médicaux</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="servicesTable" title="Répertoire des Services Médicaux" filename="services_medicaux" />
                <span class="badge bg-light text-muted border font-mono">{{ $services->count() }} service(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="servicesTable" class="table table-hover align-middle mb-0">
                    <thead style="background: #eef1f4;">
                        <tr style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7a85;">
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Nom du Service</th>
                            <th class="py-3">Code</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Actes Rattachés</th>
                            <th class="text-center py-3">Statut</th>
                            <th class="py-3">Date Création</th>
                            <th class="text-center pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr style="border-bottom: 1px solid #eef1f4;">
                                <td class="ps-4">
                                    <span class="font-mono text-muted">#{{ $service->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 28px; height: 28px; background: #e2f1ef; color: #0f766e;">
                                            <i data-lucide="building-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </div>
                                        <span>{{ $service->nom }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono px-2 py-1">{{ $service->code }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ Str::limit($service->description ?? 'Aucune description', 55) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('actes.index', ['service_id' => $service->id]) }}" class="badge badge-info-pill d-inline-flex align-items-center gap-1 font-mono text-decoration-none">
                                        <i data-lucide="file-text" style="width: 0.8rem; height: 0.8rem;"></i>
                                        <span>{{ $service->actes_count ?? 0 }} acte(s)</span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if ($service->statut)
                                        <span class="badge badge-success-pill">Actif</span>
                                    @else
                                        <span class="badge badge-danger-pill">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted font-mono">
                                        {{ $service->created_at ? $service->created_at->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-light border text-teal p-1.5" title="Voir Fiche">
                                            <i data-lucide="eye" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-light border text-muted p-1.5" title="Modifier">
                                            <i data-lucide="edit-3" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light border text-danger p-1.5" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" style="width: 0.95rem; height: 0.95rem;"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-danger text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" style="width: 1.1rem; height: 1.1rem;"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start p-4">
                                                    Êtes-vous sûr de vouloir supprimer le service <strong class="text-dark">{{ $service->nom }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light p-3">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('services.destroy', $service) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger fw-semibold">Confirmer la suppression</button>
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
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background: #e2f1ef; color: #0f766e;">
                                        <i data-lucide="building-2" style="width: 1.75rem; height: 1.75rem;"></i>
                                    </div>
                                    <div class="fw-semibold text-dark mb-1">Aucun service médical répertorié</div>
                                    <p class="text-muted small mb-3">La structure clinique n'a pas encore de service ou département enregistré.</p>
                                    <a href="{{ route('service.create') }}" class="btn btn-teal btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                                        <i data-lucide="plus-circle" style="width: 0.95rem; height: 0.95rem;"></i>
                                        <span>Créer un Service</span>
                                    </a>
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

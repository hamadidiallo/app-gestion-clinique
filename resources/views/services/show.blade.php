@extends('layout')

@section('title', 'Détails du Service Médical - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de la page --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-info-pill">Service Médical</span>
                @if ($service->statut)
                    <span class="badge badge-success-pill">Opérationnel</span>
                @else
                    <span class="badge badge-danger-pill">Inactif</span>
                @endif
            </div>
            <h1 class="h3 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <i data-lucide="building-2" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>{{ $service->nom }}</span>
                <span class="badge bg-light text-dark border font-mono fs-6">{{ $service->code }}</span>
            </h1>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('services.edit', $service) }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
                <span>Modifier</span>
            </a>
            <a href="{{ route('actes.create') }}?service_id={{ $service->id }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Créer un Acte</span>
            </a>
            <a href="{{ route('services.index') }}" class="btn btn-light border fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Fiche signalétique du service --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="info" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                        <span>Fiche Signalétique</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Nom du Département</div>
                        <div class="fw-bold text-dark fs-5 mt-1">{{ $service->nom }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Code Unique</div>
                        <div class="mt-1">
                            <span class="badge bg-light text-dark border font-mono fs-6 px-2.5 py-1">{{ $service->code }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Statut Administratif</div>
                        <div class="mt-1">
                            @if ($service->statut)
                                <span class="badge badge-success-pill">Actif & Disponible</span>
                            @else
                                <span class="badge badge-danger-pill">Désactivé / Inactif</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Description / Spécialité</div>
                        <p class="text-secondary small mt-1 mb-0 lh-base">
                            {{ $service->description ?? 'Aucune description spécifique fournie pour ce service.' }}
                        </p>
                    </div>

                    <hr style="border-color: #e6ebf0;">

                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span>Créé le :</span>
                        <span class="font-mono text-dark">{{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nomenclature des Actes Médicaux Rattachés --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="stethoscope" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                        <span>Actes Médicaux Rattachés à ce Service</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-muted border font-mono">{{ $service->actes->count() }} acte(s)</span>
                        <a href="{{ route('actes.create') }}?service_id={{ $service->id }}" class="btn btn-sm btn-teal d-flex align-items-center gap-1">
                            <i data-lucide="plus-circle" style="width: 0.85rem; height: 0.85rem;"></i>
                            <span>Nouvel Acte</span>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #eef1f4;">
                                <tr style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7a85;">
                                    <th class="ps-4 py-3">Code</th>
                                    <th class="py-3">Libellé</th>
                                    <th class="py-3">Catégorie</th>
                                    <th class="py-3 text-end">Tarif Normal</th>
                                    <th class="py-3 text-end">Tarif AMO</th>
                                    <th class="py-3 text-center">Part Médecin</th>
                                    <th class="py-3 text-center">Statut</th>
                                    <th class="pe-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($service->actes as $acte)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="ps-4 py-3">
                                            <span class="badge bg-light text-dark border font-mono fw-bold">{{ $acte->code }}</span>
                                        </td>
                                        <td class="py-3 fw-semibold">
                                            <a href="{{ route('actes.show', $acte) }}" class="text-decoration-none text-dark">
                                                {{ $acte->nom }}
                                            </a>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border small">{{ $acte->categorie_libelle }}</span>
                                        </td>
                                        <td class="py-3 text-end font-mono fw-bold">
                                            {{ number_format($acte->tarif_normal, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                        </td>
                                        <td class="py-3 text-end font-mono">
                                            @if($acte->tarif_amo !== null)
                                                <span class="text-teal fw-semibold">{{ number_format($acte->tarif_amo, 0, ',', ' ') }}</span> <small class="text-muted">FCFA</small>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center font-mono small">
                                            {{ number_format($acte->part_medecin_pourcentage, 0) }}%
                                        </td>
                                        <td class="py-3 text-center">
                                            @if($acte->statut)
                                                <span class="badge badge-success-pill">Actif</span>
                                            @else
                                                <span class="badge badge-danger-pill">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 py-3 text-center">
                                            <a href="{{ route('actes.show', $acte) }}" class="btn btn-sm btn-light border p-1" title="Voir l'acte">
                                                <i data-lucide="eye" style="width: 0.9rem; height: 0.9rem;" class="text-muted"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted small">
                                            Aucun acte médical enregistré pour ce service.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

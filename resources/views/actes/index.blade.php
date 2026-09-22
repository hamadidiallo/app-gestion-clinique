@extends('layout')

@section('title', 'Catalogue des Actes Médicaux - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="stethoscope" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Catalogue des Actes Médicaux</span>
            </h1>
            <p class="text-muted mb-0 small">Nomenclature des actes médicaux, grilles tarifaires (standard & AMO) et répartition des honoraires.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('actes.create') }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                <span>Créer un Acte</span>
            </a>
            <a href="{{ route('services.index') }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="building-2" style="width: 1rem; height: 1rem;"></i>
                <span>Voir les Services</span>
            </a>
        </div>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="row g-3 mb-4">
        {{-- Total Actes --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="file-text" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-info-pill">Nomenclature</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $stats['total'] }}
                </div>
                <div class="text-muted small">Total Actes au Catalogue</div>
            </div>
        </div>

        {{-- Actes Actifs --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Opérationnels</span>
                </div>
                <div class="font-mono fw-bold text-success mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $stats['actifs'] }}
                </div>
                <div class="text-muted small">Actes Actifs & Facturables</div>
            </div>
        </div>

        {{-- Catégories --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="layers" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-warning-pill">Typologies</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $stats['categories'] }}
                </div>
                <div class="text-muted small">Catégories d'Actes</div>
            </div>
        </div>

        {{-- Services Couverts --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #ede8f5; color: #5b3fa2;">
                        <i data-lucide="building-2" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge" style="background: #ede8f5; color: #5b3fa2; font-size: 11px; font-weight: 600;">Départements</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 24px; line-height: 1.2;">
                    {{ $stats['services'] }}
                </div>
                <div class="text-muted small">Services Médicaux Couverts</div>
            </div>
        </div>
    </div>

    {{-- Barre de Filtres & Recherche --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-3" style="border: 1px solid #e6ebf0 !important;">
        <form method="GET" action="{{ route('actes.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #cbd5e1;">
                        <i data-lucide="search" style="width: 1rem; height: 1rem;"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher par nom ou code..." value="{{ request('search') }}" style="border-color: #cbd5e1;">
                </div>
            </div>
            <div class="col-md-3">
                <select name="service_id" class="form-select" style="border-color: #cbd5e1;">
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="categorie" class="form-select" style="border-color: #cbd5e1;">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $key => $libelle)
                        <option value="{{ $key }}" {{ request('categorie') == $key ? 'selected' : '' }}>
                            {{ $libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select" style="border-color: #cbd5e1;">
                    <option value="">Tous statuts</option>
                    <option value="1" {{ request('statut') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('statut') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-teal w-100 p-2" title="Filtrer">
                    <i data-lucide="filter" style="width: 1rem; height: 1rem;"></i>
                </button>
                @if(request()->anyFilled(['search', 'service_id', 'categorie', 'statut']))
                    <a href="{{ route('actes.index') }}" class="btn btn-light border p-2" title="Réinitialiser">
                        <i data-lucide="x" style="width: 1rem; height: 1rem;"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau de données --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="list-checks" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Nomenclature & Tarifs des Actes</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="actesTable" title="Nomenclature des Actes Médicaux" filename="catalogue_actes_medicaux" />
                <span class="badge bg-light text-muted border font-mono">{{ $actes->total() }} acte(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="actesTable" class="table table-hover align-middle mb-0">
                    <thead style="background: #eef1f4;">
                        <tr style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7a85;">
                            <th class="ps-4 py-3">Code</th>
                            <th class="py-3">Libellé de l'Acte</th>
                            <th class="py-3">Catégorie</th>
                            <th class="py-3">Service de Rattachement</th>
                            <th class="py-3 text-end">Tarif Standard</th>
                            <th class="py-3 text-end">Tarif AMO Mali</th>
                            <th class="py-3 text-center">Part Médecin / Clinique</th>
                            <th class="py-3 text-center">Statut</th>
                            <th class="pe-4 py-3 text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actes as $acte)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4 py-3">
                                    <span class="badge bg-light text-dark border font-mono fw-bold" style="font-size: 12px; letter-spacing: 0.03em;">
                                        {{ $acte->code }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('actes.show', $acte) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                        {{ $acte->nom }}
                                    </a>
                                    @if($acte->description)
                                        <div class="text-muted small text-truncate" style="max-width: 280px;" title="{{ $acte->description }}">
                                            {{ $acte->description }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @php
                                        $badgeColor = match($acte->categorie) {
                                            'consultation' => 'background: #e2f1ef; color: #0f766e;',
                                            'chirurgie' => 'background: #fee2e2; color: #b91c1c;',
                                            'imagerie' => 'background: #ede8f5; color: #5b3fa2;',
                                            'biologie' => 'background: #fdf0d5; color: #8a5712;',
                                            'soins' => 'background: #e0f2fe; color: #0369a1;',
                                            'maternite' => 'background: #fce7f3; color: #be185d;',
                                            default => 'background: #f1f5f9; color: #475569;',
                                        };
                                    @endphp
                                    <span class="badge rounded-pill px-2 py-1" style="{{ $badgeColor }}; font-size: 11px; font-weight: 600;">
                                        {{ $acte->categorie_libelle }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($acte->service)
                                        <div class="d-flex align-items-center gap-1">
                                            <i data-lucide="building-2" style="width: 0.9rem; height: 0.9rem;" class="text-muted"></i>
                                            <span class="text-dark fw-medium small">{{ $acte->service->nom }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end">
                                    <span class="font-mono fw-bold text-dark" style="font-size: 13px;">
                                        {{ number_format($acte->tarif_normal, 0, ',', ' ') }}
                                    </span>
                                    <span class="text-muted small">FCFA</span>
                                </td>
                                <td class="py-3 text-end">
                                    @if($acte->tarif_amo !== null)
                                        <span class="font-mono fw-semibold text-teal" style="font-size: 13px;">
                                            {{ number_format($acte->tarif_amo, 0, ',', ' ') }}
                                        </span>
                                        <span class="text-muted small">FCFA</span>
                                    @else
                                        <span class="text-muted small">Non conventionné</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1 font-mono small">
                                        <span class="badge bg-light text-primary border" title="Part Médecin">
                                            Dr: {{ number_format($acte->part_medecin_pourcentage, 0) }}%
                                        </span>
                                        <span class="text-muted">/</span>
                                        <span class="badge bg-light text-success border" title="Part Clinique">
                                            Cl: {{ number_format($acte->part_clinique_pourcentage, 0) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    @if($acte->statut)
                                        <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                            <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span>
                                            Actif
                                        </span>
                                    @else
                                        <span class="badge badge-danger-pill d-inline-flex align-items-center gap-1">
                                            <span class="rounded-circle bg-danger" style="width: 6px; height: 6px;"></span>
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('actes.show', $acte) }}" class="btn btn-sm btn-light border p-1" title="Voir les détails" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i data-lucide="eye" style="width: 0.9rem; height: 0.9rem;" class="text-muted"></i>
                                        </a>
                                        <a href="{{ route('actes.edit', $acte) }}" class="btn btn-sm btn-light border p-1" title="Modifier l'acte" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i data-lucide="edit-3" style="width: 0.9rem; height: 0.9rem;" class="text-primary"></i>
                                        </a>
                                        <form action="{{ route('actes.destroy', $acte) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression de cet acte médical ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border p-1 text-danger" title="Supprimer" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 54px; height: 54px; background: #f8fafc;">
                                            <i data-lucide="file-x" style="width: 1.75rem; height: 1.75rem;" class="text-muted"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Aucun acte médical trouvé</h6>
                                        <p class="small mb-3 text-muted">Ajustez vos filtres ou enregistrez un nouvel acte dans la nomenclature.</p>
                                        <a href="{{ route('actes.create') }}" class="btn btn-sm btn-teal d-inline-flex align-items-center gap-1">
                                            <i data-lucide="plus" style="width: 0.85rem; height: 0.85rem;"></i>
                                            <span>Créer un Acte Médical</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($actes->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $actes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

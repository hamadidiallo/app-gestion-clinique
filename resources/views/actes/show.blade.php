@extends('layout')

@section('title', 'Détails de l\'Acte Médical - ' . $acte->nom)

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('actes.index') }}" class="text-muted text-decoration-none small d-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 0.9rem; height: 0.9rem;"></i>
                    <span>Catalogue des Actes</span>
                </a>
            </div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="file-text" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>{{ $acte->nom }}</span>
            </h1>
            <div class="d-flex align-items-center gap-2 text-muted small mt-1">
                <span class="badge bg-light text-dark border font-mono fw-bold">{{ $acte->code }}</span>
                <span>•</span>
                <span>{{ $acte->categorie_libelle }}</span>
                @if($acte->service)
                    <span>•</span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <i data-lucide="building-2" style="width: 0.85rem; height: 0.85rem;"></i>
                        {{ $acte->service->nom }}
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('actes.edit', $acte) }}" class="btn btn-teal fw-semibold d-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
                <span>Modifier l'Acte</span>
            </a>
            <a href="{{ route('tickets.create') }}?acte_id={{ $acte->id }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="receipt" style="width: 1rem; height: 1rem;"></i>
                <span>Facturer cet Acte</span>
            </a>
            <form action="{{ route('actes.destroy', $acte) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet acte médical ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2" title="Supprimer">
                    <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                    <span>Supprimer</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Cartes de Synthèse Financière --}}
    <div class="row g-3 mb-4">
        {{-- Tarif Standard --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="coins" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-info-pill">Comptant / Privé</span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 22px; line-height: 1.2;">
                    {{ number_format($acte->tarif_normal, 0, ',', ' ') }} <small class="text-muted fs-6">FCFA</small>
                </div>
                <div class="text-muted small">Tarif Standard Normal</div>
            </div>
        </div>

        {{-- Tarif AMO --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="shield-check" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge badge-success-pill">Convention AMO</span>
                </div>
                <div class="font-mono fw-bold text-teal mb-1" style="font-size: 22px; line-height: 1.2;">
                    @if($acte->tarif_amo !== null)
                        {{ number_format($acte->tarif_amo, 0, ',', ' ') }} <small class="text-muted fs-6">FCFA</small>
                    @else
                        <span class="text-muted fs-6">Non conventionné</span>
                    @endif
                </div>
                <div class="text-muted small">Plafond Remboursement AMO</div>
            </div>
        </div>

        {{-- Part Médecin --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #e0f2fe; color: #0369a1;">
                        <i data-lucide="user-check" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge rounded-pill bg-light text-primary border font-mono">
                        {{ number_format($acte->part_medecin_pourcentage, 0) }}%
                    </span>
                </div>
                <div class="font-mono fw-bold text-primary mb-1" style="font-size: 22px; line-height: 1.2;">
                    {{ number_format($acte->tarif_normal * ($acte->part_medecin_pourcentage / 100), 0, ',', ' ') }} <small class="text-muted fs-6">FCFA</small>
                </div>
                <div class="text-muted small">Honoraires Médecin / Acte</div>
            </div>
        </div>

        {{-- Part Clinique --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-3" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="building" style="width: 1.25rem; height: 1.25rem;"></i>
                    </div>
                    <span class="badge rounded-pill bg-light text-warning border font-mono">
                        {{ number_format($acte->part_clinique_pourcentage, 0) }}%
                    </span>
                </div>
                <div class="font-mono fw-bold text-dark mb-1" style="font-size: 22px; line-height: 1.2;">
                    {{ number_format($acte->tarif_normal * ($acte->part_clinique_pourcentage / 100), 0, ',', ' ') }} <small class="text-muted fs-6">FCFA</small>
                </div>
                <div class="text-muted small">Revenus Clinique / Acte</div>
            </div>
        </div>
    </div>

    {{-- Détails et Spécifications --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4" style="border: 1px solid #e6ebf0 !important;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i data-lucide="align-left" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                        <span>Fiche Technique & Paramètres de l'Acte</span>
                    </h5>
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
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Code Nomenclature</div>
                            <div class="font-mono fw-bold text-dark fs-6">{{ $acte->code }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Catégorie d'Acte</div>
                            <div class="fw-semibold text-dark">{{ $acte->categorie_libelle }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Service Médical de Rattachement</div>
                            <div class="fw-semibold text-dark">
                                @if($acte->service)
                                    <a href="{{ route('services.show', $acte->service) }}" class="text-decoration-none text-teal">
                                        {{ $acte->service->nom }} ({{ $acte->service->code }})
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Tarif Assurances Privées Conventionnées</div>
                            <div class="font-mono fw-semibold text-dark">
                                @if($acte->tarif_specifique !== null)
                                    {{ number_format($acte->tarif_specifique, 0, ',', ' ') }} FCFA
                                @else
                                    <span class="text-muted small">Même que le tarif standard</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <div class="text-muted small mb-1">Description & Protocoles Médicaux</div>
                            <div class="p-3 bg-light rounded-2 text-dark small" style="border: 1px solid #e2e8f0;">
                                {{ $acte->description ?: 'Aucune consigne clinique ou protocole particulier renseigné pour cet acte.' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 px-4 border-top text-muted small d-flex justify-content-between">
                    <span>Créé le {{ $acte->created_at ? $acte->created_at->format('d/m/Y à H:i') : '—' }}</span>
                    <span>Dernière mise à jour le {{ $acte->updated_at ? $acte->updated_at->format('d/m/Y à H:i') : '—' }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Carte d'actions rapides --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4" style="border: 1px solid #e6ebf0 !important;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="zap" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                    <span>Actions & Intégration</span>
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('tickets.create') }}" class="btn btn-teal fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="receipt" style="width: 1rem; height: 1rem;"></i>
                        <span>Émettre un Ticket de Caisse</span>
                    </a>
                    <a href="{{ route('actes.create') }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="plus-circle" style="width: 1rem; height: 1rem;"></i>
                        <span>Ajouter un Nouvel Acte</span>
                    </a>
                    <a href="{{ route('actes.index') }}" class="btn btn-light border d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                        <span>Retour au Catalogue</span>
                    </a>
                </div>
            </div>
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

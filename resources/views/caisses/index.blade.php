@extends('layout')

@section('title', 'Gestion des Caisses & Guichets - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec titre, description et boutons d'actions rapides --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>{{ !empty($isCaissier) ? 'Mon Guichet' : 'Caisse & Rapprochement' }}</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">{{ !empty($isCaissier) ? 'Mes Sessions' : 'Supervision Guichets' }}</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">{{ !empty($isCaissier) ? 'Mes Sessions de Caisse' : 'Sessions de Caisses' }}</h1>
        </div>
        <div class="d-flex gap-2">
            @if(empty($activeCaisse))
                <a href="{{ route('caisses.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i data-lucide="plus-circle" class="lucide-sm"></i>
                    <span>{{ !empty($isCaissier) ? 'Ouvrir ma Caisse' : 'Ouvrir une Caisse' }}</span>
                </a>
            @else
                <a href="{{ route('caisses.show', $activeCaisse) }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i data-lucide="wallet" class="lucide-sm"></i>
                    <span>Accéder à ma Caisse</span>
                </a>
            @endif
            <a href="{{ route('mouvementcaisses.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left-right" class="lucide-sm"></i>
                <span>Mouvement Espèces</span>
            </a>
        </div>
    </div>

    {{-- Bandeau d'état personnalisé pour la Caissière --}}
    @if(!empty($isCaissier))
        @if(!$activeCaisse)
            <div class="card border-0 shadow-sm p-4 mb-4 rounded-3" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-left: 5px solid #d97706 !important;">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #fde68a; color: #92400e;">
                            <i data-lucide="lock" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Votre session de caisse est actuellement fermée</h5>
                            <p class="text-muted mb-0 small">Pour pouvoir enregistrer les paiements de tickets et délivrer des reçus, vous devez ouvrir votre session journalière.</p>
                        </div>
                    </div>
                    <a href="{{ route('caisses.create') }}" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 text-nowrap shadow-sm">
                        <i data-lucide="plus-circle" class="lucide-sm"></i>
                        <span>Ouvrir ma caisse</span>
                    </a>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm p-4 mb-4 rounded-3" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-left: 5px solid #059669 !important;">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #a7f3d0; color: #065f46;">
                            <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold mb-0 text-dark">Session de Caisse #{{ $activeCaisse->id }} en cours</h5>
                                <span class="badge badge-success-pill">Ouverte</span>
                            </div>
                            <p class="text-muted mb-0 small">
                                Ouverte le {{ \Carbon\Carbon::parse($activeCaisse->date_ouverture)->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }} ·
                                Solde actuel : <strong class="font-mono text-dark fw-bold">{{ number_format($activeCaisse->solde_actuel, 0, ',', ' ') }} FCFA</strong>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('caisses.show', $activeCaisse) }}" class="btn btn-primary px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-nowrap shadow-sm">
                            <i data-lucide="layout-dashboard" class="lucide-sm"></i>
                            <span>Gérer ma session</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Composant de Filtrage par Période --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

    {{-- Synthèse sous forme de cartes d'indicateurs (Style doc/Clinique.dc.html) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="banknote" class="lucide"></i>
                    </div>
                    <span class="badge {{ !empty($activeCaisse) || $caisses->where('statut', 'ouverte')->count() > 0 ? 'badge-success-pill' : 'badge-warning-pill' }}">
                        {{ !empty($isCaissier) ? (!empty($activeCaisse) ? 'Ouverte' : 'Fermée') : 'En service' }}
                    </span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">
                    {{ !empty($isCaissier) ? (!empty($activeCaisse) ? 'Ouverte' : 'Fermée') : $caisses->where('statut', 'ouverte')->count() }}
                </div>
                <div class="small text-muted">{{ !empty($isCaissier) ? 'Statut de ma caisse' : 'Caisses actuellement ouvertes' }}</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="wallet" class="lucide"></i>
                    </div>
                    <span class="badge badge-info-pill">Théorique</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">
                    {{ number_format($caisses->sum('solde_theorique'), 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">{{ !empty($isCaissier) ? 'Mon solde théorique cumulé' : 'Solde théorique cumulé' }}</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="arrow-down-left" class="lucide"></i>
                    </div>
                    <span class="badge badge-success-pill">Entrées</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">
                    {{ number_format($caisses->sum('total_entrees'), 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">{{ !empty($isCaissier) ? 'Mes entrées d\'espèces' : 'Entrées d\'espèces reçues' }}</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="scale" class="lucide"></i>
                    </div>
                    <span class="badge badge-warning-pill">Écarts</span>
                </div>
                <div class="font-mono fs-4 fw-bold {{ $caisses->sum('ecart') != 0 ? 'text-danger' : 'text-dark' }} mb-1">
                    {{ number_format($caisses->sum('ecart'), 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">{{ !empty($isCaissier) ? 'Mon écart cumulé' : 'Écart de caisse global' }}</div>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif des sessions de caisses --}}
    <div class="card overflow-hidden">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="history" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">{{ !empty($isCaissier) ? 'Historique de mes Sessions de Caisse' : 'Historique des Sessions & Guichets' }}</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="caissesTable" title="Historique des Sessions de Caisse" filename="sessions_caisse" />
                <span class="badge bg-light text-muted border font-mono">{{ $caisses->count() }} session(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="caissesTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 90px;">Session</th>
                            <th>Caissier Responsable</th>
                            <th>Ouverture</th>
                            <th class="text-end">Fonds Initial</th>
                            <th class="text-end">Entrées</th>
                            <th class="text-end">Sorties</th>
                            <th class="text-end">Solde Théorique</th>
                            <th class="text-center">Écart</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caisses as $caisse)
                            <tr>
                                <td class="ps-3">
                                    <span class="font-mono text-muted small">#{{ $caisse->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e; font-size: 0.75rem;">
                                            {{ strtoupper(substr($caisse->user->name ?? $caisse->user->nom ?? 'C', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $caisse->user->name ?? $caisse->user->nom ?? 'Non attribué' }}</div>
                                            <small class="text-muted">{{ $caisse->user->role->nom ?? 'Guichetier' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono small text-dark">
                                        {{ $caisse->date_ouverture ? \Carbon\Carbon::parse($caisse->date_ouverture)->format('d/m/Y H:i') : '—' }}
                                    </span>
                                </td>
                                <td class="text-end font-mono text-muted small">{{ number_format($caisse->fonds_initial, 0, ',', ' ') }} F</td>
                                <td class="text-end font-mono fw-bold" style="color: #0f6b5f;">+{{ number_format($caisse->total_entrees, 0, ',', ' ') }} F</td>
                                <td class="text-end font-mono fw-bold text-danger">-{{ number_format($caisse->total_sorties, 0, ',', ' ') }} F</td>
                                <td class="text-end font-mono fw-bold text-dark">{{ number_format($caisse->solde_theorique, 0, ',', ' ') }} F</td>
                                <td class="text-center">
                                    @if ($caisse->ecart < 0)
                                        <span class="badge badge-danger-pill font-mono">
                                            {{ number_format($caisse->ecart, 0, ',', ' ') }} F
                                        </span>
                                    @elseif ($caisse->ecart > 0)
                                        <span class="badge badge-warning-pill font-mono">
                                            +{{ number_format($caisse->ecart, 0, ',', ' ') }} F
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border font-mono">0 F</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($caisse->statut == 'ouverte')
                                        <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #12a594; display: inline-block;"></span>
                                            <span>Ouverte</span>
                                        </span>
                                    @elseif ($caisse->statut == 'fermee')
                                        <span class="badge bg-light text-muted border">
                                            Fermée
                                        </span>
                                    @else
                                        <span class="badge badge-info-pill">
                                            Vérifiée
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('caisses.show', $caisse) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" title="Voir Fiche">
                                            <i data-lucide="eye" class="lucide-sm"></i>
                                            <span>Détails</span>
                                        </a>
                                        @if($caisse->statut == 'ouverte')
                                            <a href="{{ route('caisses.edit', $caisse) }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-1" title="Clôturer la Caisse">
                                                <i data-lucide="lock" class="lucide-sm"></i>
                                                <span>Clôturer</span>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCaisseModal{{ $caisse->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" class="lucide-sm"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deleteCaisseModal{{ $caisse->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white py-2">
                                                    <h6 class="modal-title d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" class="lucide-sm"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start py-4">
                                                    Êtes-vous certain de vouloir supprimer définitivement cette session de caisse ?
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('caisses.destroy', $caisse) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer définitivement</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i data-lucide="banknote" class="lucide-lg d-block mx-auto mb-2 text-muted opacity-50"></i>
                                    Aucune session de caisse trouvée pour cette période.
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

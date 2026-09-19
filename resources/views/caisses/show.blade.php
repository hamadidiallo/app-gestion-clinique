@extends('layout')

@section('title', 'Session de Caisse #' . $caisse->id . ' - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête & Actions Rapides --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <a href="{{ route('caisses.index') }}" class="text-decoration-none text-muted">Sessions de Caisse</a>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Détail Session #{{ $caisse->id }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h3 fw-bold text-dark mb-0">Session de Caisse #{{ $caisse->id }}</h1>
                @if ($caisse->statut === 'ouverte')
                    <span class="badge badge-success-pill d-inline-flex align-items-center gap-1 px-3 py-1">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #12a594; display: inline-block;"></span>
                        <span>SESSION ACTIVE</span>
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-1">
                        SESSION CLÔTURÉE
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('caisses.print', $caisse) }}" target="_blank" class="btn btn-outline-dark d-inline-flex align-items-center gap-2">
                <i data-lucide="printer" class="lucide-sm"></i>
                <span>Imprimer Ticket Z (80mm)</span>
            </a>

            @if ($caisse->statut === 'ouverte')
                <a href="{{ route('mouvementcaisses.create', ['caisse_id' => $caisse->id]) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i data-lucide="arrow-left-right" class="lucide-sm"></i>
                    <span>Nouveau Mouvement</span>
                </a>

                <button type="button" class="btn btn-warning d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#clotureModal">
                    <i data-lucide="lock" class="lucide-sm"></i>
                    <span class="fw-semibold">Clôturer la Session</span>
                </button>
            @endif

            <a href="{{ route('caisses.index') }}" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left" class="lucide-sm"></i>
            </a>
        </div>
    </div>

    {{-- Cartes Financières --}}
    <div class="row g-3 mb-4">
        {{-- Fond Initial --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Fond Initial</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f1f5f9; color: #475569;">
                        <i data-lucide="wallet" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">
                    {{ number_format($caisse->fonds_initial, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">Monnaie au départ</div>
            </div>
        </div>

        {{-- Total Entrées --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Total Encaissements</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="arrow-down-left" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-success mb-1">
                    +{{ number_format($caisse->total_entrees, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">Tickets & prestations</div>
            </div>
        </div>

        {{-- Total Sorties --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold text-uppercase">Total Dépenses / Sorties</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fee2e2; color: #991b1b;">
                        <i data-lucide="arrow-up-right" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-danger mb-1">
                    -{{ number_format($caisse->total_sorties, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">Dépenses et retraits</div>
            </div>
        </div>

        {{-- Solde Théorique Attendu --}}
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100" style="background: #f0fdfa; border: 1px solid #ccfbf1;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-dark fw-bold text-uppercase">Solde Attendu en Caisse</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #0f766e; color: white;">
                        <i data-lucide="calculator" class="lucide-sm"></i>
                    </div>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">
                    {{ number_format($caisse->solde_theorique, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                </div>
                <div class="small text-muted">Fond + Entrées - Sorties</div>
            </div>
        </div>
    </div>

    {{-- Bilan de Rapprochement (Si Clôturée) --}}
    @if ($caisse->statut === 'fermee')
        <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid {{ $caisse->ecart == 0 ? '#12a594' : ($caisse->ecart < 0 ? '#dc2626' : '#d97706') }} !important;">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Espèces Réelles Comptées (Physique)</div>
                        <div class="font-mono fs-3 fw-bold text-dark">
                            {{ number_format($caisse->solde_physique, 0, ',', ' ') }} <span class="fs-6 text-muted">FCFA</span>
                        </div>
                        <small class="text-muted">Clôturé le {{ $caisse->date_fermeture ? \Carbon\Carbon::parse($caisse->date_fermeture)->format('d/m/Y à H:i') : '-' }}</small>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Résultat du Rapprochement (Écart)</div>
                        @if ($caisse->ecart == 0)
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-success-pill fs-6 px-3 py-2">
                                    <i data-lucide="check-circle-2" class="lucide-sm me-1"></i> Parfaitement Équilibrée (0 F)
                                </span>
                            </div>
                        @elseif ($caisse->ecart < 0)
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-danger-pill fs-6 px-3 py-2 font-mono">
                                    <i data-lucide="alert-triangle" class="lucide-sm me-1"></i> Déficit : {{ number_format($caisse->ecart, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-warning-pill fs-6 px-3 py-2 font-mono">
                                    <i data-lucide="info" class="lucide-sm me-1"></i> Surplus : +{{ number_format($caisse->ecart, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Observations de Clôture</div>
                        <p class="text-dark mb-0 small fst-italic">
                            "{{ $caisse->observation ?? 'Aucune observation saisie.' }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Détails de la Session & Mouvements --}}
    <div class="card overflow-hidden shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="receipt" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Journal des Mouvements d'Espèces de la Session</h5>
            </div>
            <span class="badge bg-light text-muted border font-mono">{{ $caisse->mouvements->count() }} opération(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 80px;">Heure</th>
                            <th>Type</th>
                            <th>Motif / Libellé</th>
                            <th>Opérateur</th>
                            <th class="text-end pe-3">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caisse->mouvements as $mouvement)
                            <tr>
                                <td class="ps-3 font-mono text-muted small">
                                    {{ $mouvement->created_at ? $mouvement->created_at->format('H:i') : '-' }}
                                </td>
                                <td>
                                    @if ($mouvement->type_mouvement === 'entree')
                                        <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="arrow-down-left" class="lucide-xs"></i> Entrée
                                        </span>
                                    @else
                                        <span class="badge badge-danger-pill d-inline-flex align-items-center gap-1">
                                            <i data-lucide="arrow-up-right" class="lucide-xs"></i> Sortie
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $mouvement->motif ?? 'Encaissement guichet' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $mouvement->user->name ?? $mouvement->user->nom ?? 'Guichetier' }}</small>
                                </td>
                                <td class="text-end pe-3 font-mono fw-bold {{ $mouvement->type_mouvement === 'entree' ? 'text-success' : 'text-danger' }}">
                                    {{ $mouvement->type_mouvement === 'entree' ? '+' : '-' }}{{ number_format($mouvement->montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i data-lucide="inbox" class="lucide-lg d-block mx-auto mb-2 opacity-50"></i>
                                    Aucun mouvement d'espèces enregistré pour le moment dans cette session.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modale Interactive de Clôture de Session --}}
@if ($caisse->statut === 'ouverte')
    <div class="modal fade" id="clotureModal" tabindex="-1" aria-labelledby="clotureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="clotureModalLabel">
                        <i data-lucide="lock" class="lucide-sm"></i>
                        <span>Clôture de Caisse & Rapprochement</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <form action="{{ route('caisses.cloturer', $caisse) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Solde théorique rappelé --}}
                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-semibold">Solde Théorique Attendu :</span>
                                <span class="font-mono fw-bold fs-5 text-dark" id="soldeTheoriqueVal" data-val="{{ $caisse->solde_theorique }}">
                                    {{ number_format($caisse->solde_theorique, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        </div>

                        {{-- Saisie du comptage physique --}}
                        <div class="mb-3">
                            <label for="solde_physique" class="form-label fw-bold text-dark">
                                Espèces Comptées dans le Tiroir (FCFA) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="100" min="0" name="solde_physique" id="solde_physique" class="form-control font-mono fw-bold" placeholder="0" required autofocus>
                                <span class="input-group-text font-mono fw-bold bg-white text-muted">FCFA</span>
                            </div>
                            <small class="text-muted">Comptez billets et pièces présents dans la caisse.</small>
                        </div>

                        {{-- Indicateur dynamique d'écart --}}
                        <div id="ecartPreviewBox" class="p-3 rounded-3 mb-3 d-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold small" id="ecartLabel">Écart de caisse :</span>
                                <span class="font-mono fw-bold" id="ecartVal">0 FCFA</span>
                            </div>
                            <div class="small mt-1" id="ecartMsg"></div>
                        </div>

                        {{-- Observation --}}
                        <div class="mb-2">
                            <label for="observation_cloture" class="form-label fw-semibold text-dark">Observations / Justifications</label>
                            <textarea name="observation" id="observation_cloture" class="form-control" rows="2" placeholder="Ex: Fin de vacation, caisse transmise au superviseur..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning fw-bold d-inline-flex align-items-center gap-2">
                            <i data-lucide="check" class="lucide-sm"></i>
                            <span>Confirmer & Clôturer la Session</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const soldeTheorique = parseFloat(document.getElementById('soldeTheoriqueVal').getAttribute('data-val')) || 0;
            const soldePhysiqueInput = document.getElementById('solde_physique');
            const ecartBox = document.getElementById('ecartPreviewBox');
            const ecartVal = document.getElementById('ecartVal');
            const ecartMsg = document.getElementById('ecartMsg');

            soldePhysiqueInput.addEventListener('input', function() {
                const entered = parseFloat(this.value);
                if (isNaN(entered)) {
                    ecartBox.classList.add('d-none');
                    return;
                }

                ecartBox.classList.remove('d-none');
                const diff = entered - soldeTheorique;
                const formattedDiff = Math.abs(diff).toLocaleString('fr-FR') + ' FCFA';

                if (diff === 0) {
                    ecartBox.style.background = '#e3f3ee';
                    ecartBox.style.border = '1px solid #12a594';
                    ecartVal.className = 'font-mono fw-bold text-success';
                    ecartVal.textContent = '0 FCFA (Caisse parfaite)';
                    ecartMsg.textContent = '✅ Aucun écart constaté. La caisse concorde parfaitement.';
                    ecartMsg.className = 'small mt-1 text-success';
                } else if (diff < 0) {
                    ecartBox.style.background = '#fee2e2';
                    ecartBox.style.border = '1px solid #ef4444';
                    ecartVal.className = 'font-mono fw-bold text-danger';
                    ecartVal.textContent = '- ' + formattedDiff;
                    ecartMsg.textContent = '⚠️ Manquant constaté dans la caisse par rapport aux encaissements.';
                    ecartMsg.className = 'small mt-1 text-danger';
                } else {
                    ecartBox.style.background = '#fef3c7';
                    ecartBox.style.border = '1px solid #f59e0b';
                    ecartVal.className = 'font-mono fw-bold text-warning';
                    ecartVal.textContent = '+ ' + formattedDiff;
                    ecartMsg.textContent = 'ℹ️ Surplus constaté dans la caisse.';
                    ecartMsg.className = 'small mt-1 text-dark';
                }
            });
        });
    </script>
@endif
@endsection

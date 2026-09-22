@extends('layout')

@section('title', 'Bulletin de Rémunération - Dr. ' . ($remuneration->medecin->nom ?? '') . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('remunerations.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour au registre des paies
                </a>
                <span class="text-muted small">|</span>
                <span class="badge bg-light text-dark border small font-mono">{{ $remuneration->reference }}</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="file-check-2" class="text-teal"></i>
                Bulletin de Décompte : Dr. {{ $remuneration->medecin ? $remuneration->medecin->prenom . ' ' . $remuneration->medecin->nom : 'N/A' }}
            </h1>
            <p class="text-muted mb-0 small">
                Période du <strong>{{ $remuneration->periode_debut ? $remuneration->periode_debut->format('d/m/Y') : '-' }}</strong> 
                au <strong>{{ $remuneration->periode_fin ? $remuneration->periode_fin->format('d/m/Y') : '-' }}</strong> 
                &bull; Spécialité : {{ $remuneration->medecin->specialite ?? 'Médecine Générale' }}
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                <span>Imprimer</span>
            </button>

            @if(!in_array($remuneration->statut, ['payee', 'paye']))
                <form action="{{ route('remunerations.payer', $remuneration) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer le paiement effectif de {{ number_format($remuneration->montant_medecin, 0, ',', ' ') }} FCFA ?');">
                    @csrf
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2 px-3 shadow-sm">
                        <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                        <span>Valider le Règlement</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('remunerations.edit', $remuneration) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3">
                <i data-lucide="edit-2" style="width: 16px; height: 16px;"></i>
                <span>Modifier</span>
            </a>
        </div>
    </div>

    {{-- Synthèse Financière du Décompte --}}
    <div class="row g-3 mb-4">
        {{-- Part Nette Médecin --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white border-start border-4 border-success h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Rétrocession Nette Praticien</span>
                    <div class="bg-success-subtle text-success p-2 rounded-circle">
                        <i data-lucide="banknote" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-success font-mono">
                    {{ number_format($remuneration->montant_medecin, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <div class="d-flex align-items-center gap-2 mt-2">
                    @if(in_array($remuneration->statut, ['payee', 'paye']))
                        <span class="badge bg-success text-white px-2 py-1 rounded-pill small">
                            <i data-lucide="check" style="width: 12px; height: 12px;" class="me-1"></i>Réglement effectué
                        </span>
                    @else
                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small">
                            <i data-lucide="clock" style="width: 12px; height: 12px;" class="me-1"></i>En attente de paiement
                        </span>
                    @endif
                    <span class="text-muted small">
                        {{ $remuneration->type_remuneration === 'salaire_fixe' ? 'Forfait Fixe' : ($remuneration->pourcentage . '% des actes') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Part Conservée Clinique --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white border-start border-4 border-primary h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Quote-Part Conservée Clinique</span>
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                        <i data-lucide="building" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-primary font-mono">
                    {{ number_format($remuneration->montant_clinique, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <div class="text-muted small mt-2">
                    Contribution aux frais de plateau technique et gestion
                </div>
            </div>
        </div>

        {{-- Volume Base Facturée --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white border-start border-4 border-teal h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Base Globale Facturée (Actes)</span>
                    <div class="bg-teal-subtle text-teal p-2 rounded-circle">
                        <i data-lucide="activity" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-dark font-mono">
                    {{ number_format($remuneration->montant_base, 0, ',', ' ') }} <small class="fs-5 text-muted">FCFA</small>
                </h2>
                <div class="text-muted small mt-2">
                    {{ $prestations->count() }} prestation(s) médicale(s) réalisée(s)
                </div>
            </div>
        </div>
    </div>

    {{-- Détail des Soins / Actes Réalisés --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="list-checks" class="text-teal"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Détail des Soins & Actes Réalisés par le Praticien sur la Période</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-3 py-1 rounded-pill">
                    {{ $prestations->count() }} soin(s) comptabilisé(s)
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3" style="width: 140px;">Date & Heure</th>
                            <th>Patient</th>
                            <th>Acte Médical / Soin</th>
                            <th>Département</th>
                            <th class="text-end">Tarif Soin</th>
                            <th class="text-end">Part Médecin</th>
                            <th class="text-end pe-3">Part Clinique</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestations as $p)
                            <tr>
                                <td class="ps-3 small font-mono">
                                    {{ $p->date_prestation ? $p->date_prestation->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $p->patient ? $p->patient->nom . ' ' . $p->patient->prenom : 'Patient #' . $p->patient_id }}
                                    </div>
                                    <div class="small text-muted font-mono">
                                        {{ $p->patient->numero_dossier ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $p->acte->nom ?? $p->type }}
                                    </div>
                                    @if($p->acte && $p->acte->code)
                                        <span class="badge bg-light text-muted border font-mono small">
                                            {{ $p->acte->code }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $p->service->nom ?? 'Général' }}
                                    </span>
                                </td>
                                <td class="text-end font-mono fw-semibold text-dark">
                                    {{ number_format($p->montant, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-end font-mono">
                                    <span class="fw-bold text-success">
                                        {{ number_format($p->part_medecin, 0, ',', ' ') }}
                                    </span>
                                    <small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-end font-mono text-primary pe-3">
                                    {{ number_format($p->part_clinique, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-lucide="info" style="width: 36px; height: 36px;" class="mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Aucune prestation individuelle enregistrée pour ce médecin sur cet intervalle de dates.</p>
                                        @if($remuneration->type_remuneration === 'salaire_fixe')
                                            <p class="small text-muted mt-1">(Ce médecin bénéficie d'un <strong>salaire fixe mensuel</strong> indépendant du volume d'actes).</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($prestations->count() > 0)
                        <tfoot class="table-light fw-bold font-mono">
                            <tr>
                                <td colspan="4" class="ps-3 text-uppercase small text-dark">Total des prestations du décompte :</td>
                                <td class="text-end text-dark">{{ number_format($prestations->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                <td class="text-end text-success">{{ number_format($prestations->sum('part_medecin'), 0, ',', ' ') }} FCFA</td>
                                <td class="text-end text-primary pe-3">{{ number_format($prestations->sum('part_clinique'), 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Métadonnées de validation & Notes --}}
    <div class="card border-0 shadow-sm rounded-3 bg-light">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <span class="text-muted small d-block">Agent validateur</span>
                    <strong class="text-dark">{{ $remuneration->user->name ?? $remuneration->user->nom ?? 'Administration' }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Date effective du règlement</span>
                    <strong class="text-dark">{{ $remuneration->date_paiement ? $remuneration->date_paiement->format('d/m/Y') : 'En attente de versement' }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Identifiant fiche</span>
                    <span class="font-mono text-muted small">{{ $remuneration->reference }}</span>
                </div>
                @if($remuneration->description)
                    <div class="col-12 mt-2 pt-2 border-top">
                        <span class="text-muted small d-block">Notes / Observations :</span>
                        <p class="small text-dark mb-0">{{ $remuneration->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

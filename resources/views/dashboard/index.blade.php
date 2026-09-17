@extends('layout')

@section('title', 'Tableau de Bord Financier & Activité')

@section('content')
    <section>
        {{-- En-tête principal & Barre de Filtres Temporels --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                    <span>Supervision</span>
                    <span class="opacity-50">/</span>
                    <span class="fw-semibold text-dark">Contrôle Financier & Activité</span>
                </div>
                <h1 class="h3 fw-bold mb-0 text-dark">Tableau de Bord</h1>
            </div>

            {{-- Filtres temporels par boutons radio / liens --}}
            <div class="d-flex align-items-center gap-2 bg-white p-1 rounded-3 border">
                @foreach($periodesLabels as $key => $label)
                    <a href="{{ route('dashboard', ['periode' => $key]) }}" 
                       class="btn btn-sm {{ $periode === $key ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }}" 
                       style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Ligne 1 : Cartes KPI Financières Principales (Style doc/Clinique.dc.html) --}}
        <div class="row g-3 mb-4">
            {{-- Recettes Totales --}}
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                            <i data-lucide="trending-up" class="lucide"></i>
                        </div>
                        <span class="badge badge-success-pill">Recettes</span>
                    </div>
                    <div class="font-mono fs-4 fw-bold text-dark mb-1">
                        {{ number_format($totalRecettes, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                    </div>
                    <div class="small text-muted">Recettes encaissées</div>
                </div>
            </div>

            {{-- Dépenses Totales --}}
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fce4e4; color: #b3261e;">
                            <i data-lucide="trending-down" class="lucide"></i>
                        </div>
                        <span class="badge badge-danger-pill">Charges</span>
                    </div>
                    <div class="font-mono fs-4 fw-bold text-dark mb-1">
                        {{ number_format($totalDepenses, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                    </div>
                    <div class="small text-muted">Dépenses & charges</div>
                </div>
            </div>

            {{-- Solde Net --}}
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                            <i data-lucide="scale" class="lucide"></i>
                        </div>
                        <span class="badge {{ $soldeNet >= 0 ? 'badge-info-pill' : 'badge-danger-pill' }}">Net</span>
                    </div>
                    <div class="font-mono fs-4 fw-bold {{ $soldeNet >= 0 ? 'text-dark' : 'text-danger' }} mb-1">
                        {{ number_format($soldeNet, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                    </div>
                    <div class="small text-muted">Solde d'exploitation net</div>
                </div>
            </div>

            {{-- Créances & Dettes Patients --}}
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                            <i data-lucide="alert-circle" class="lucide"></i>
                        </div>
                        <span class="badge badge-warning-pill">À recouvrer</span>
                    </div>
                    <div class="font-mono fs-4 fw-bold text-dark mb-1">
                        {{ number_format($totalDettesRestantes, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                    </div>
                    <div class="small text-muted">Impayés & dettes actives</div>
                </div>
            </div>
        </div>

        {{-- Ligne 2 : Cartes KPI Secondaires --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Tickets & Facturation</span>
                        <i data-lucide="receipt" class="lucide text-muted"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mt-2">
                        <span class="font-mono fs-3 fw-bold text-dark">{{ $nombreTickets }}</span>
                        <span class="font-mono fs-5 fw-bold" style="color: var(--primary-color);">{{ number_format($totalMontantTickets, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="small text-muted mt-2">Volume et valeur totale des actes facturés</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Honoraires Praticiens</span>
                        <i data-lucide="user-check" class="lucide text-muted"></i>
                    </div>
                    <div class="font-mono fs-3 fw-bold text-dark mt-2">
                        {{ number_format($partsMedecinsDues, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                    </div>
                    <div class="small text-muted mt-2">Rétrocessions médicales dues en attente</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Guichets & Sessions</span>
                        <i data-lucide="banknote" class="lucide text-muted"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="font-mono fs-3 fw-bold text-dark">{{ $caissesOuvertes->count() }}</span>
                        <span class="badge badge-success-pill">En service</span>
                    </div>
                    <div class="mt-2 text-truncate small">
                        @forelse($caissesOuvertes as $caisse)
                            <span class="badge badge-info-pill me-1">#{{ $caisse->id }} {{ $caisse->user->name ?? '' }}</span>
                        @empty
                            <span class="text-muted">Aucun guichet actuellement ouvert.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Ligne 3 : Tableau de Synthèse des Prestations par Service --}}
        <div class="card overflow-hidden mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="building-2" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                    <h2 class="h6 mb-0 fw-bold text-dark">Chiffre d'Affaires par Service Médical</h2>
                </div>
                <span class="small text-muted">{{ count($servicesStats) }} service(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Code</th>
                            <th>Service Médical</th>
                            <th>Actes au Catalogue</th>
                            <th class="text-end">C.A. Généré</th>
                            <th class="text-center" style="width: 120px;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicesStats as $service)
                            <tr>
                                <td><span class="font-mono text-muted small">{{ $service->code }}</span></td>
                                <td><strong class="text-dark">{{ $service->nom }}</strong></td>
                                <td><span class="text-muted">{{ $service->prestations_count }} prestation(s)</span></td>
                                <td class="text-end font-mono fw-bold" style="color: var(--primary-color);">
                                    {{ number_format($service->chiffre_affaires, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    @if($service->statut)
                                        <span class="badge badge-success-pill">Actif</span>
                                    @else
                                        <span class="badge badge-danger-pill">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucun service répertorié.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

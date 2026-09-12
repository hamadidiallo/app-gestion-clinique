@extends('layout')

@section('title', 'Tableau de Bord Financier')

@section('content')
    <section class="mt-4">
        {{-- En-tête principal & Barre de Filtres Temporels --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h2 mb-1">Tableau de Bord Financier & Administrative</h1>
                <p class="text-muted mb-0">Indicateurs clés du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}</p>
            </div>

            {{-- Filtres temporels par boutons radio / liens --}}
            <div class="btn-group shadow-sm" role="group" aria-label="Filtre par période">
                @foreach($periodesLabels as $key => $label)
                    <a href="{{ route('dashboard', ['periode' => $key]) }}" class="btn btn-outline-primary {{ $periode === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Ligne 1 : Cartes KPI Financières Principales --}}
        <div class="row g-3 mb-4">
            {{-- Recettes Totales --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Recettes Encaissement</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($totalRecettes, 2, ',', ' ') }} FCFA</h3>
                        <small class="text-white-50">Période : {{ $periodesLabels[$periode] }}</small>
                    </div>
                </div>
            </div>

            {{-- Dépenses Totales --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Dépenses & Charges</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($totalDepenses, 2, ',', ' ') }} FCFA</h3>
                        <small class="text-white-50">Période : {{ $periodesLabels[$periode] }}</small>
                    </div>
                </div>
            </div>

            {{-- Solde Net --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm {{ $soldeNet >= 0 ? 'bg-primary' : 'bg-dark' }} text-white">
                    <div class="card-body">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Solde Financier Net</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($soldeNet, 2, ',', ' ') }} FCFA</h3>
                        <small class="text-white-50">Recettes - Dépenses</small>
                    </div>
                </div>
            </div>

            {{-- Créances & Dettes Patients --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <h6 class="text-dark-50 text-uppercase fw-bold mb-2">Dettes & Reste à Recouvrer</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($totalDettesRestantes, 2, ',', ' ') }} FCFA</h3>
                        <small class="text-dark-50">Cumul général des impayés</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ligne 2 : Cartes KPI Secondaires (Actes & Caisses) --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-2">Facturation & Tickets Émis</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-2 fw-bold text-dark">{{ $nombreTickets }}</span>
                            <span class="fs-5 text-primary fw-bold">{{ number_format($totalMontantTickets, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <p class="text-muted small mb-0 mt-2">Volume total des tickets enregistrés sur la période.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-2">Honoraires Médecins à Payer</h6>
                        <span class="fs-2 fw-bold text-danger">{{ number_format($partsMedecinsDues, 2, ',', ' ') }} FCFA</span>
                        <p class="text-muted small mb-0 mt-2">Part des rétrocessions en attente de versement.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase mb-2">Caisses Actuellement Ouvertes</h6>
                        <span class="fs-2 fw-bold text-success">{{ $caissesOuvertes->count() }} Session(s)</span>
                        <div class="mt-2">
                            @forelse($caissesOuvertes as $caisse)
                                <span class="badge bg-success me-1">#{{ $caisse->id }} - {{ $caisse->user->name ?? '' }}</span>
                            @empty
                                <span class="text-muted small">Aucune caisse ouverte.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ligne 3 : Tableau de Synthèse des Prestations par Service --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Synthèse de l'Activité par Service Médical</h5>
                <span class="badge bg-primary">CLINGEST V1</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Service Médical</th>
                            <th>Nombre de Prestations</th>
                            <th>Chiffre d'Affaires Généré</th>
                            <th>Statut Service</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicesStats as $service)
                            <tr>
                                <td><code>{{ $service->code }}</code></td>
                                <td><strong>{{ $service->nom }}</strong></td>
                                <td>{{ $service->prestations_count }} prestation(s) au catalogue</td>
                                <td><strong class="text-success">{{ number_format($service->chiffre_affaires, 2, ',', ' ') }} FCFA</strong></td>
                                <td>
                                    @if($service->statut)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun service répertorié.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

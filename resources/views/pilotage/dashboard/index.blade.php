@extends('layout')

@section('title', $isCaissier ? 'Tableau de Bord - Espace Guichet' : 'Tableau de Bord Financier & Activité')

@section('content')
    <section>
        {{-- En-tête principal & Barre de Filtres Temporels --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                    <span>{{ $isCaissier ? 'Accueil Guichet' : 'Supervision' }}</span>
                    <span class="opacity-50">/</span>
                    <span class="fw-semibold text-dark">{{ $isCaissier ? 'Opérations & Encaissements' : 'Contrôle Financier & Activité' }}</span>
                </div>
                <h1 class="h3 fw-bold mb-0 text-dark">{{ $isCaissier ? 'Tableau de Bord Guichet' : 'Tableau de Bord' }}</h1>
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

        @if($isCaissier)
            {{-- ========================================================================= --}}
            {{-- VUE ADAPTÉE POUR LE GUICHET / CAISSIER (Données confidentielles masquées)   --}}
            {{-- ========================================================================= --}}

            {{-- Bannière d'état de la session de caisse --}}
            @if($maCaisse)
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-3" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-left: 5px solid #059669 !important;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #a7f3d0; color: #065f46;">
                                <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold mb-0 text-dark">Session de Caisse #{{ $maCaisse->reference ?? $maCaisse->id }} active</h5>
                                    <span class="badge badge-success-pill">Guichet Ouvert</span>
                                </div>
                                <p class="text-muted mb-0 small">
                                    Ouverte le {{ $maCaisse->date_ouverture ? \Carbon\Carbon::parse($maCaisse->date_ouverture)->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') : '-' }} ·
                                    Solde attendu en caisse : <strong class="font-mono text-dark fw-bold">{{ number_format($maCaisse->solde_actuel, 0, ',', ' ') }} FCFA</strong>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('caisses.show', $maCaisse) }}" class="btn btn-primary px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-nowrap shadow-sm">
                                <i data-lucide="layout-dashboard" class="lucide-sm"></i>
                                <span>Gérer ma session</span>
                            </a>
                            <a href="{{ route('caisses.edit', $maCaisse) }}" class="btn btn-outline-danger px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 text-nowrap bg-white">
                                <i data-lucide="lock" class="lucide-sm"></i>
                                <span>Clôturer</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-3" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-left: 5px solid #d97706 !important;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #fde68a; color: #92400e;">
                                <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Aucune session de caisse active</h5>
                                <p class="text-muted mb-0 small">
                                    Votre guichet est actuellement fermé. Pour enregistrer des encaissements et émettre des tickets, veuillez ouvrir une session de caisse.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('caisses.create') }}" class="btn btn-primary px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 text-nowrap shadow-sm">
                            <i data-lucide="plus-circle" class="lucide-sm"></i>
                            <span>Ouvrir ma caisse</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- 4 Cartes KPI Opérationnelles du Guichet --}}
            <div class="row g-3 mb-4">
                {{-- Mes Encaissements --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                                <i data-lucide="trending-up" class="lucide"></i>
                            </div>
                            <span class="badge badge-success-pill">Mes Encaissements</span>
                        </div>
                        <div class="font-mono fs-4 fw-bold text-dark mb-1">
                            {{ number_format($mesEncaissements, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                        </div>
                        <div class="small text-muted">Règlements perçus ({{ $periodesLabels[$periode] ?? 'période' }})</div>
                    </div>
                </div>

                {{-- Solde Actuel en Caisse --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                                <i data-lucide="banknote" class="lucide"></i>
                            </div>
                            <span class="badge {{ $maCaisse ? 'badge-info-pill' : 'badge-danger-pill' }}">
                                {{ $maCaisse ? 'Session Active' : 'Caisse Fermée' }}
                            </span>
                        </div>
                        <div class="font-mono fs-4 fw-bold text-dark mb-1">
                            {{ number_format($maCaisse ? $maCaisse->solde_actuel : 0, 0, ',', ' ') }} <small class="fs-6 fw-normal text-muted">FCFA</small>
                        </div>
                        <div class="small text-muted">Espèces attendues au guichet</div>
                    </div>
                </div>

                {{-- Mes Tickets Émis --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7;">
                                <i data-lucide="receipt" class="lucide"></i>
                            </div>
                            <span class="badge badge-info-pill">Mes Tickets Émis</span>
                        </div>
                        <div class="font-mono fs-4 fw-bold text-dark mb-1">
                            {{ $mesTicketsCount }} <small class="fs-6 fw-normal text-muted">ticket(s)</small>
                        </div>
                        <div class="small text-muted">{{ number_format($mesTicketsMontant, 0, ',', ' ') }} FCFA facturés</div>
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
                        <div class="small text-muted">Impayés patients au guichet</div>
                    </div>
                </div>
            </div>

            {{-- Actions Rapides Guichet --}}
            <div class="card p-3 mb-4 bg-white border">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">Actions Rapides du Guichet :</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                            <i data-lucide="plus-circle" class="lucide-sm"></i>
                            <span>Nouveau Ticket</span>
                        </a>
                        <a href="{{ route('patients.create') }}" class="btn btn-sm btn-outline-teal d-inline-flex align-items-center gap-1">
                            <i data-lucide="user-plus" class="lucide-sm"></i>
                            <span>Nouveau Patient</span>
                        </a>
                        <a href="{{ route('caisses.index') }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 text-dark">
                            <i data-lucide="banknote" class="lucide-sm"></i>
                            <span>Historique Caisse</span>
                        </a>
                        <a href="{{ route('dettes.index') }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 text-dark">
                            <i data-lucide="alert-triangle" class="lucide-sm"></i>
                            <span>Dettes & Impayés</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Mes Derniers Tickets Émis --}}
            <div class="card overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="clock" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                        <h2 class="h6 mb-0 fw-bold text-dark">Mes Derniers Tickets Émis</h2>
                    </div>
                    <a href="{{ route('tickets.index') }}" class="small text-primary text-decoration-none fw-semibold">Voir tous mes tickets &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Réf. Ticket</th>
                                <th>Date / Heure</th>
                                <th>Patient</th>
                                <th>Service</th>
                                <th class="text-end">Montant</th>
                                <th class="text-center" style="width: 120px;">Statut</th>
                                <th class="text-end" style="width: 110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($derniersTickets as $tck)
                                <tr>
                                    <td><span class="font-mono fw-bold text-dark small">{{ $tck->reference ?? '#'.$tck->id }}</span></td>
                                    <td class="small text-muted">{{ $tck->date_ticket ? $tck->date_ticket->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $tck->patient ? $tck->patient->nom_complet : 'Patient anonyme' }}</strong>
                                        @if($tck->assurance)
                                            <div class="small text-muted">{{ $tck->assurance->nom }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $tck->service->nom ?? 'Général' }}</span></td>
                                    <td class="text-end font-mono fw-bold text-dark">
                                        {{ number_format($tck->montant_patient > 0 ? $tck->montant_patient : $tck->montant_total, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-center">
                                        @if($tck->statut === 'paye')
                                            <span class="badge badge-success-pill">Payé</span>
                                        @elseif($tck->statut === 'partiel')
                                            <span class="badge badge-warning-pill">Partiel</span>
                                        @else
                                            <span class="badge badge-danger-pill">En attente</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('tickets.show', $tck) }}" class="btn btn-sm btn-light border p-1" title="Voir détails">
                                                <i data-lucide="eye" class="lucide-sm"></i>
                                            </a>
                                            <a href="{{ route('tickets.print', $tck) }}" target="_blank" class="btn btn-sm btn-light border p-1" title="Imprimer le ticket">
                                                <i data-lucide="printer" class="lucide-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Aucun ticket émis récemment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            {{-- ========================================================================= --}}
            {{-- VUE MACRO POUR ADMINISTRATEUR & COMPTABLE                                  --}}
            {{-- ========================================================================= --}}

            {{-- Ligne 1 : Cartes KPI Financières Principales --}}
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
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $service->actes_count }} acte(s)</span>
                                        @if($service->prestations_periode_count > 0)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1 font-mono" title="Prestations réalisées sur la période sélectionnée">
                                                {{ $service->prestations_periode_count }} réalisée(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end font-mono fw-bold" style="color: var(--primary-color);">
                                        <div>{{ number_format($service->chiffre_affaires, 0, ',', ' ') }} FCFA</div>
                                        @if($service->recettes_encaissees > 0 && $service->montant_facture != $service->recettes_encaissees)
                                            <div class="small text-muted fw-normal" style="font-size: 0.75rem;">
                                                Encaissé : {{ number_format($service->recettes_encaissees, 0, ',', ' ') }} F | Facturé : {{ number_format($service->montant_facture, 0, ',', ' ') }} F
                                            </div>
                                        @endif
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
        @endif
    </section>
@endsection

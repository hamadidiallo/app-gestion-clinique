@extends('layout')

@section('title', 'Abonnement & Formules de Licence - CLINGEST')

@section('content')
<div class="container-fluid p-0">

    {{-- Fil d'Ariane et En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Administration</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Licence & Facturation</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">Abonnement & Formules de Licence</h1>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#contactLicenseModal">
                <i data-lucide="headphones" class="lucide-sm"></i>
                <span>Contacter le Support Commercial</span>
            </button>
        </div>
    </div>

    {{-- Bandeau Résumé Licence Actuelle de la Clinique --}}
    @if($clinique)
    <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #092c28 0%, #0f766e 65%, #14b8a6 100%); color: #ffffff; border-radius: 14px;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-dark fw-bold text-uppercase px-2 py-1 font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            Plan Actuel : {{ strtoupper($clinique->plan ?? 'Standard') }}
                        </span>
                        @if($clinique->estActive())
                            <span class="badge bg-emerald-500 text-white d-inline-flex align-items-center gap-1" style="background: #10b981;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #ffffff; display: inline-block;"></span>
                                <span>Licence Active</span>
                            </span>
                        @else
                            <span class="badge bg-danger text-white">Licence Expirée ou Suspendue</span>
                        @endif
                    </div>
                    <h2 class="h4 fw-bold mb-1 text-white">{{ $clinique->nom }}</h2>
                    <p class="text-white-50 small mb-0">
                        {{ $clinique->ville }} ({{ $clinique->pays }}) · Code établissement : <strong class="font-mono text-white">{{ $clinique->code }}</strong> · Code invitation : <strong class="font-mono text-white">{{ $clinique->code_invitation }}</strong>
                    </p>
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-3">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center" style="min-width: 140px; backdrop-filter: blur(8px);">
                            <div class="text-white-50 small mb-1">Expiration</div>
                            <div class="fw-bold font-mono text-white">
                                {{ $clinique->date_expiration ? $clinique->date_expiration->format('d/m/Y') : 'Illimitée' }}
                            </div>
                        </div>

                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center" style="min-width: 140px; backdrop-filter: blur(8px);">
                            <div class="text-white-50 small mb-1">Validité Restante</div>
                            <div class="fw-bold font-mono text-white">
                                @if($clinique->joursRestantsAbonnement() !== null)
                                    {{ $clinique->joursRestantsAbonnement() }} jours
                                @else
                                    Permanent
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sélecteur Période Mensuel / Annuel --}}
    <div class="text-center mb-5">
        <h2 class="h4 fw-bold text-dark mb-2">Choisissez la formule adaptée à votre établissement</h2>
        <p class="text-muted small mb-4" style="max-width: 580px; margin: auto;">
            Toutes nos formules incluent l'isolation complète des données multi-cliniques, les mises à jour de sécurité et la conformité aux normes comptables OHADA & fiscales locales.
        </p>

        <div class="d-inline-flex align-items-center p-1 bg-white border rounded-pill shadow-sm">
            <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold" id="btnBillingMonthly" onclick="setBillingCycle('monthly')" style="background: var(--primary-color); color: #fff;">
                Facturation Mensuelle
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold text-muted" id="btnBillingYearly" onclick="setBillingCycle('yearly')">
                Facturation Annuelle
                <span class="badge rounded-pill ms-1" style="background: #e2f1ef; color: #0f766e; font-size: 0.72rem;">2 mois offerts (-17%)</span>
            </button>
        </div>
    </div>

    {{-- Cartes des Formules d'Abonnement --}}
    <div class="row g-4 mb-5 align-items-stretch">
        @foreach($formules as $formule)
            @php
                $isCurrent = strtolower($clinique->plan ?? '') === strtolower($formule['id']);
            @endphp
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card w-100 border-0 shadow-sm position-relative d-flex flex-column {{ $formule['is_popular'] ? 'ring-teal' : '' }}" 
                     style="border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; {{ $formule['is_popular'] ? 'border: 2px solid #0f766e !important;' : 'border: 1px solid #e2e8f0 !important;' }}">
                    
                    @if($formule['is_popular'])
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge text-white px-3 py-1 shadow-sm font-semibold rounded-pill" style="background: #0f766e; font-size: 0.75rem;">
                                {{ $formule['badge'] }}
                            </span>
                        </div>
                    @endif

                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        {{-- En-tête formule --}}
                        <div class="mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <h3 class="h5 fw-bold text-dark mb-0">{{ $formule['nom'] }}</h3>
                                @if(!$formule['is_popular'])
                                    <span class="badge rounded-pill px-2 py-1 text-uppercase fw-semibold" style="background: {{ $formule['badge_bg'] }}; color: {{ $formule['badge_color'] }}; font-size: 0.68rem; letter-spacing: 0.4px;">
                                        {{ $formule['badge'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0" style="min-height: 44px; line-height: 1.5;">{{ $formule['description'] }}</p>
                        </div>

                        {{-- Prix --}}
                        <div class="p-3 rounded-3 bg-light border mb-4">
                            <div class="price-monthly">
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="font-mono fs-2 fw-bold text-dark">{{ number_format($formule['prix_mensuel'], 0, ',', ' ') }}</span>
                                    <span class="text-muted small fw-semibold">FCFA / mois</span>
                                </div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Facturé mensuellement sans engagement</div>
                            </div>
                            <div class="price-yearly" style="display: none;">
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="font-mono fs-2 fw-bold text-teal" style="color: #0f766e;">{{ number_format($formule['prix_annuel'], 0, ',', ' ') }}</span>
                                    <span class="text-muted small fw-semibold">FCFA / an</span>
                                </div>
                                <div class="text-teal small fw-semibold" style="font-size: 0.75rem; color: #0f766e;">
                                    Équivalent à {{ number_format(round($formule['prix_annuel'] / 12), 0, ',', ' ') }} FCFA / mois
                                </div>
                            </div>
                        </div>

                        {{-- Bouton d'action --}}
                        <div class="mb-4">
                            @if($isCurrent)
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 fw-bold" disabled>
                                    <i data-lucide="check-circle" class="lucide-sm text-success"></i>
                                    <span>Votre Formule Actuelle</span>
                                </button>
                            @else
                                <button type="button" class="btn {{ $formule['is_popular'] ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2 d-flex align-items-center justify-content-center gap-2 fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#contactLicenseModal" data-formule="{{ $formule['nom'] }}">
                                    <span>Choisir cette Formule</span>
                                    <i data-lucide="arrow-right" class="lucide-sm"></i>
                                </button>
                            @endif
                        </div>

                        {{-- Liste des caractéristiques --}}
                        <div class="flex-grow-1 pt-3 border-top">
                            <div class="small fw-bold text-dark text-uppercase letter-spacing-1 mb-3" style="font-size: 0.72rem;">
                                Fonctionnalités incluses :
                            </div>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                                @foreach($formule['caracteristiques'] as $feature)
                                    <li class="d-flex align-items-start gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                             style="width: 18px; height: 18px; background: #e2f1ef; color: #0f766e;">
                                            <i data-lucide="check" style="width: 12px; height: 12px; stroke-width: 2.5;"></i>
                                        </div>
                                        <span class="text-dark" style="font-size: 0.84rem; line-height: 1.45;">{{ $feature }}</span>
                                    </li>
                                @endforeach

                                @foreach($formule['non_inclus'] as $missing)
                                    <li class="d-flex align-items-start gap-2 text-muted opacity-50">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                             style="width: 18px; height: 18px; background: #f1f5f9; color: #94a3b8;">
                                            <i data-lucide="minus" style="width: 12px; height: 12px; stroke-width: 2;"></i>
                                        </div>
                                        <span style="font-size: 0.84rem; line-height: 1.45;">{{ $missing }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tableau Comparatif Complet --}}
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 14px;">
        <div class="card-header bg-white py-3 border-bottom">
            <h3 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="table" class="lucide-sm text-teal"></i>
                <span>Matrice Comparative Détaillée des Formules</span>
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 35%;">Modules & Capacités</th>
                            <th class="text-center" style="width: 21%;">Cabinet / Starter</th>
                            <th class="text-center bg-teal-subtle text-teal fw-bold" style="width: 22%; background-color: #f0fdfa;">Pro (Clinique)</th>
                            <th class="text-center" style="width: 22%;">Entreprise (Hôpital)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Nombre de Guichets de Caisse</td>
                            <td class="text-center font-mono">1 Guichet</td>
                            <td class="text-center font-mono fw-bold text-teal" style="background-color: #f0fdfa;">Jusqu'à 5 Guichets</td>
                            <td class="text-center font-mono fw-bold">Illimités</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Médecins & Collaborateurs</td>
                            <td class="text-center font-mono">3 comptes</td>
                            <td class="text-center font-mono fw-bold text-teal" style="background-color: #f0fdfa;">15 comptes</td>
                            <td class="text-center font-mono fw-bold">Illimités</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Dossiers Médicaux & Tickets Thermiques</td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                            <td class="text-center text-success" style="background-color: #f0fdfa;"><i data-lucide="check" class="lucide-sm"></i></td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Calcul Automatique des Honoraires Médicaux</td>
                            <td class="text-center text-muted"><i data-lucide="x" class="lucide-sm"></i></td>
                            <td class="text-center text-success" style="background-color: #f0fdfa;"><i data-lucide="check" class="lucide-sm"></i></td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Facturation Tiers-Payant (AMO / INPS / Assurances)</td>
                            <td class="text-center text-muted"><i data-lucide="x" class="lucide-sm"></i></td>
                            <td class="text-center text-success" style="background-color: #f0fdfa;"><i data-lucide="check" class="lucide-sm"></i></td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Journal d'Encaissement & Exports Excel / PDF</td>
                            <td class="text-center text-muted"><i data-lucide="x" class="lucide-sm"></i></td>
                            <td class="text-center text-success" style="background-color: #f0fdfa;"><i data-lucide="check" class="lucide-sm"></i></td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Journal d'Audit & Traçabilité Complète</td>
                            <td class="text-center text-muted"><i data-lucide="x" class="lucide-sm"></i></td>
                            <td class="text-center text-muted" style="background-color: #f0fdfa;"><i data-lucide="x" class="lucide-sm"></i></td>
                            <td class="text-center text-success"><i data-lucide="check" class="lucide-sm"></i></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">Support & Assistance Technique</td>
                            <td class="text-center text-muted small">E-mail (48h)</td>
                            <td class="text-center fw-semibold text-teal small" style="background-color: #f0fdfa;">WhatsApp & Téléphone (6j/7)</td>
                            <td class="text-center fw-semibold text-dark small">Dédié 24/7 + Gestionnaire</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Foire Aux Questions (FAQ) --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100 p-4 border-0 shadow-sm rounded-3">
                <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold">
                    <i data-lucide="help-circle" class="lucide-sm text-teal"></i>
                    <span>Comment s'effectue le paiement de la licence ?</span>
                </div>
                <p class="text-muted small mb-0">
                    Les règlements s'effectuent par Orange Money, Moov Money, virement bancaire ou chèque d'entreprise. Une facture acquittée avec quittance fiscale vous est délivrée après validation.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 p-4 border-0 shadow-sm rounded-3">
                <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold">
                    <i data-lucide="refresh-cw" class="lucide-sm text-teal"></i>
                    <span>Puis-je changer de formule à tout moment ?</span>
                </div>
                <p class="text-muted small mb-0">
                    Oui. Vous pouvez faire évoluer votre clinique vers une formule supérieure (par exemple passer de Starter à Pro) sans aucune interruption de service et avec conservation intégrale de vos données.
                </p>
            </div>
        </div>
    </div>

</div>

{{-- MODAL CONTACT & DEMANDE DE MISE À NIVEAU --}}
<div class="modal fade" id="contactLicenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3" style="background: #0f766e; color: #ffffff;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i data-lucide="sparkles" class="lucide-sm"></i>
                    <span>Service Licence & Abonnement</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <p class="text-muted small mb-3">
                    Pour renouveler votre formule, passer à un forfait supérieur ou activer des modules complémentaires pour <strong>{{ $clinique->nom ?? 'votre clinique' }}</strong>, contactez directement votre conseiller commercial :
                </p>

                <div class="list-group list-group-flush border rounded-3 mb-3">
                    <a href="https://wa.me/22376000000?text=Bonjour,%20je%20souhaite%20renouveler%20ou%20changer%20la%20licence%20pour%20la%20clinique%20{{ urlencode($clinique->nom ?? '') }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #dcfce7; color: #166534;">
                                <i data-lucide="message-square" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">WhatsApp Commercial</div>
                                <div class="text-muted small">(+223) 76 00 00 00</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="lucide-sm text-muted"></i>
                    </a>

                    <a href="tel:+22320220000" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7;">
                                <i data-lucide="phone-call" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Ligne Téléphonique Directe</div>
                                <div class="text-muted small">(+223) 20 22 00 00</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="lucide-sm text-muted"></i>
                    </a>

                    <a href="mailto:support@clingest.com?subject=Licence%20Clinique%20{{ urlencode($clinique->nom ?? '') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #ede9fe; color: #6d28d9;">
                                <i data-lucide="mail" class="lucide-sm"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Courrier Électronique</div>
                                <div class="text-muted small">support@clingest.com</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="lucide-sm text-muted"></i>
                    </a>
                </div>

                <div class="bg-light p-3 rounded-3 text-muted small border">
                    <i data-lucide="shield-check" class="lucide-xs text-teal me-1"></i>
                    Votre identifiant de clinique à rappeler : <strong class="font-mono text-dark">{{ $clinique->code ?? 'CLA' }}</strong> (Code d'invitation : <span class="font-mono">{{ $clinique->code_invitation ?? 'N/A' }}</span>).
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setBillingCycle(cycle) {
        const btnM = document.getElementById('btnBillingMonthly');
        const btnY = document.getElementById('btnBillingYearly');
        const monthPrices = document.querySelectorAll('.price-monthly');
        const yearPrices = document.querySelectorAll('.price-yearly');

        if (cycle === 'yearly') {
            btnY.style.background = 'var(--primary-color)';
            btnY.style.color = '#fff';
            btnY.classList.remove('text-muted');

            btnM.style.background = 'transparent';
            btnM.style.color = 'var(--text-muted)';
            btnM.classList.add('text-muted');

            monthPrices.forEach(el => el.style.display = 'none');
            yearPrices.forEach(el => el.style.display = 'block');
        } else {
            btnM.style.background = 'var(--primary-color)';
            btnM.style.color = '#fff';
            btnM.classList.remove('text-muted');

            btnY.style.background = 'transparent';
            btnY.style.color = 'var(--text-muted)';
            btnY.classList.add('text-muted');

            monthPrices.forEach(el => el.style.display = 'block');
            yearPrices.forEach(el => el.style.display = 'none');
        }
    }
</script>
@endpush
@endsection

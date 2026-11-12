@extends('layout')

@section('title', 'Règlement de Solde & Encaissement d\'Impayé - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Caisse & Facturation</span>
                <span class="opacity-50">/</span>
                <a href="{{ route('paiements.index') }}" class="text-decoration-none text-muted">Journal des Paiements</a>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Règlement de Solde</span>
            </div>
            <h1 class="h3 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <i data-lucide="hand-coins" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <span>Encaisser un Reste à Payer (Dette Ticket)</span>
            </h1>
            <p class="text-muted small mb-0 mt-1">Enregistrement d'un règlement complémentaire sur un ticket débiteur ou solde d'hospitalisation.</p>
        </div>
        <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i data-lucide="arrow-left" class="lucide-sm"></i>
            <span>Historique des Paiements</span>
        </a>
    </div>

    {{-- SI UN TICKET SPÉCIFIQUE EST SÉLECTIONNÉ : BANNIÈRE D'INFORMATION RECAP --}}
    @if($selectedTicket)
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden" style="border-left: 5px solid var(--primary-color) !important;">
            <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #0f766e, #14b8a6); flex-shrink: 0;">
                        <i data-lucide="receipt" class="lucide-md"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0">Ticket #{{ $selectedTicket->reference }}</h5>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small fw-bold">Impayé / Partiel</span>
                        </div>
                        <div class="text-muted small mt-1">
                            Patient : <strong class="text-dark">{{ $selectedTicket->patient->nom ?? 'Inconnu' }} {{ $selectedTicket->patient->prenom ?? '' }}</strong>
                            (Tél: {{ $selectedTicket->patient->telephone ?? 'N/A' }})
                            • Émis le {{ $selectedTicket->date_ticket ? $selectedTicket->date_ticket->format('d/m/Y H:i') : $selectedTicket->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <span class="text-muted small d-block">Solde Restant Dû (Dette)</span>
                    <strong class="fs-4 font-mono fw-bold text-danger">{{ number_format($selectedTicket->reste_a_payer, 0, ',', ' ') }} FCFA</strong>
                    <div class="extra-small text-muted">Sur un total net de {{ number_format($selectedTicket->montant_patient, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('paiements.store') }}" method="POST" id="form_paiement">
        @csrf

        <div class="row g-4">
            {{-- COLONNE GAUCHE : SÉLECTION DU TICKET ET DÉTAILS --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e;">
                                <i data-lucide="receipt" class="lucide-sm"></i>
                            </span>
                            Ticket de Caisse à Solder
                        </h5>
                        <span class="badge bg-light text-muted border px-2 py-1 small">Tickets Débiteurs</span>
                    </div>

                    <div class="card-body p-4">
                        @if($tickets->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i data-lucide="check-check" class="lucide-lg mb-2 text-success" style="width: 48px; height: 48px;"></i>
                                <h5 class="fw-bold text-dark mt-2">Aucun ticket en attente de solde !</h5>
                                <p class="small text-muted mb-0">Tous les tickets et soins de la clinique sont entièrement réglés. Aucune dette patient en cours.</p>
                                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i data-lucide="plus" class="lucide-xs me-1"></i> Créer un nouveau ticket
                                </a>
                            </div>
                        @else
                            {{-- Champ de recherche dynamique pour filtrer les tickets débiteurs --}}
                            <div class="mb-3">
                                <label for="ticket_search_input" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="search" class="lucide-sm text-primary"></i> Recherche Rapide de Ticket Débiteur
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted border-end-0">
                                        <i data-lucide="search" class="lucide-sm text-primary"></i>
                                    </span>
                                    <input type="text" id="ticket_search_input" class="form-control border-start-0" placeholder="Tapez le nom, n° de téléphone ou référence du ticket..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary" id="btn_clear_ticket_search" title="Effacer">
                                        <i data-lucide="x" class="lucide-xs"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Champ caché transmis au formulaire --}}
                            <input type="hidden" name="ticket_id" id="ticket_id" value="{{ $selectedTicketId ?? (old('ticket_id') ?? ($tickets->first()->id ?? '')) }}" required>

                            {{-- Liste de cartes cliquables pour sélection fluide --}}
                            <div class="mb-2">
                                <label class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between mb-2">
                                    <span>Sélectionner le Ticket Débiteur <span class="text-danger">*</span></span>
                                    <span class="text-muted extra-small" id="count_tickets_label">{{ $tickets->count() }} ticket(s) avec dette</span>
                                </label>

                                <div class="d-flex flex-column gap-2" id="tickets_cards_container" style="max-height: 270px; overflow-y: auto; padding-right: 4px;">
                                    @foreach($tickets as $index => $ticket)
                                        @php
                                            $pNom = $ticket->patient->nom ?? 'Inconnu';
                                            $pPrenom = $ticket->patient->prenom ?? '';
                                            $pTel = $ticket->patient->telephone ?? '';
                                            $searchContent = strtolower("{$ticket->reference} {$pNom} {$pPrenom} {$pTel}");
                                            $isSelected = ($selectedTicketId == $ticket->id) || (old('ticket_id') == $ticket->id) || (!$selectedTicketId && !old('ticket_id') && $index === 0);
                                        @endphp
                                        <div class="ticket-card p-3 rounded-3 border bg-white cursor-pointer position-relative transition-all {{ $isSelected ? 'selected' : '' }}"
                                             data-id="{{ $ticket->id }}"
                                             data-reference="{{ $ticket->reference }}"
                                             data-patient="{{ trim("{$pNom} {$pPrenom}") }}"
                                             data-tel="{{ $pTel }}"
                                             data-total="{{ (float)$ticket->montant_patient }}"
                                             data-reste="{{ (float)$ticket->reste_a_payer }}"
                                             data-search="{{ $searchContent }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-3">
                                                    {{-- Indicateur de sélection radio visuel --}}
                                                    <div class="ticket-check rounded-circle border d-flex align-items-center justify-content-center flex-shrink-0" 
                                                         style="width: 22px; height: 22px; {{ $isSelected ? 'background-color: #0f766e; border-color: #0f766e;' : 'background-color: #f8fafc;' }}">
                                                        <i data-lucide="check" class="lucide-xs text-white {{ $isSelected ? '' : 'd-none' }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-0.5">
                                                            <strong class="font-mono text-dark fs-6">#{{ $ticket->reference }}</strong>
                                                            <span class="text-muted extra-small">{{ $ticket->date_ticket ? $ticket->date_ticket->format('d/m/Y') : $ticket->created_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        <div class="text-dark fw-bold small">{{ trim("{$pNom} {$pPrenom}") }}</div>
                                                        <div class="text-muted extra-small font-mono">{{ $pTel ? 'Tél: ' . $pTel : 'Sans téléphone' }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-muted extra-small d-block mb-1">Reste à payer</span>
                                                    <span class="badge bg-danger-subtle text-danger font-mono fw-bold px-2.5 py-1.5 fs-6 border border-danger-subtle rounded-pill">
                                                        {{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('ticket_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Encadré récapitulatif du ticket sélectionné --}}
                            <div class="p-3 rounded-3 bg-light border mt-3" id="recap_selected_ticket_box">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Patient concerné :</span>
                                    <strong class="text-dark" id="disp_patient_name">--</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Total Net du Ticket :</span>
                                    <strong class="font-mono text-dark" id="disp_ticket_total">0 FCFA</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top text-danger">
                                    <span class="fw-bold small">Reste à payer (Dette active) :</span>
                                    <strong class="font-mono fs-5 text-danger" id="disp_ticket_reste">0 FCFA</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- INFORMATIONS ADMINISTRATIVES --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="lucide-sm text-primary"></i>
                            <span>Référant & Prise en Charge</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="type_payeur" class="form-label fw-bold text-dark small">Type de Payeur <span class="text-danger">*</span></label>
                                <select name="type_payeur" id="type_payeur" class="form-select @error('type_payeur') is-invalid @enderror" required>
                                    <option value="patient" {{ old('type_payeur') == 'patient' ? 'selected' : '' }}>Patient (Direct)</option>
                                    <option value="assurance" {{ old('type_payeur') == 'assurance' ? 'selected' : '' }}>Organisme Assurance</option>
                                    <option value="tiers" {{ old('type_payeur') == 'tiers' ? 'selected' : '' }}>Tiers Payeur / Employeur</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="assurance_id" class="form-label fw-bold text-dark small">Assurance (si applicable)</label>
                                <select name="assurance_id" id="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                                    <option value="">-- Aucune --</option>
                                    @foreach($assurances as $ass)
                                        <option value="{{ $ass->id }}" {{ old('assurance_id') == $ass->id ? 'selected' : '' }}>{{ $ass->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="user_id" class="form-label fw-bold text-dark small">Agent Caissier <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('user_id', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE : ENCAISSEMENT & MONNAIE --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">
                            <i data-lucide="wallet" class="lucide-sm"></i>
                        </span>
                        Encaissement au Guichet
                    </h5>

                    <div class="row g-3">
                        {{-- Référence du reçu --}}
                        <div class="col-12">
                            <label for="reference" class="form-label fw-bold text-dark small">N° de Reçu de Caisse <span class="text-danger">*</span></label>
                            <input type="text" name="reference" id="reference" class="form-control font-mono fw-semibold bg-light" value="{{ old('reference', $defaultReference) }}" required readonly>
                        </div>

                        {{-- Mode de paiement --}}
                        <div class="col-12">
                            <label for="mode_paiement_id" class="form-label fw-bold text-dark small">Mode d'Encaissement <span class="text-danger">*</span></label>
                            <select name="mode_paiement_id" id="mode_paiement_id" class="form-select fw-semibold @error('mode_paiement_id') is-invalid @enderror" required>
                                @foreach($modePaiements as $mode)
                                    <option value="{{ $mode->id }}" {{ old('mode_paiement_id') == $mode->id ? 'selected' : '' }}>
                                        {{ $mode->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 1. Montant Reçu --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="montant_recu" class="form-label fw-bold text-dark small mb-0">Somme Reçue en Espèces / Transfert <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 small fw-bold" id="btn_tout_solder">
                                    Tout solder
                                </button>
                            </div>
                            <div class="input-group input-group-lg">
                                <input type="number" step="100" min="0" name="montant_recu" id="montant_recu" class="form-control font-mono fw-bold text-dark @error('montant_recu') is-invalid @enderror" value="{{ old('montant_recu') }}" placeholder="0" required>
                                <span class="input-group-text bg-white font-mono fw-bold">FCFA</span>
                            </div>
                            <div class="extra-small text-muted mt-1">Montant remis par le patient ou déposé à la caisse.</div>
                        </div>

                        {{-- 2. Montant Imputé --}}
                        <div class="col-12">
                            <label for="montant_impute" class="form-label fw-bold text-dark small">Montant Imputé sur la Dette <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="100" min="0" name="montant_impute" id="montant_impute" class="form-control font-mono fw-bold text-success @error('montant_impute') is-invalid @enderror" value="{{ old('montant_impute') }}" placeholder="0" required>
                                <span class="input-group-text bg-white font-mono fw-bold text-success">FCFA</span>
                            </div>
                            <div class="extra-small text-muted mt-1">Partie réellement déduite de la dette du ticket.</div>
                        </div>

                        {{-- 3. Monnaie Rendue --}}
                        <div class="col-12">
                            <label for="montant_rendu" class="form-label fw-bold text-warning small">Monnaie à Rendre au Patient (Calcul Auto)</label>
                            <div class="input-group">
                                <input type="number" step="1" min="0" name="montant_rendu" id="montant_rendu" class="form-control font-mono fw-bold text-warning bg-light" value="{{ old('montant_rendu', 0) }}" readonly>
                                <span class="input-group-text bg-warning-subtle font-mono fw-bold text-warning">FCFA</span>
                            </div>
                            <div class="extra-small text-muted mt-1">Différence calculée automatiquement (Somme reçue - Montant imputé).</div>
                        </div>

                        {{-- Date & Statut --}}
                        <div class="col-md-7">
                            <label for="date_paiement" class="form-label fw-bold text-dark small">Date & Heure Règlement <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_paiement" id="date_paiement" class="form-control font-mono small" value="{{ old('date_paiement', date('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="col-md-5">
                            <label for="statut" class="form-label fw-bold text-dark small">Statut <span class="text-danger">*</span></label>
                            <select name="statut" id="statut" class="form-select small">
                                <option value="valide" selected>Validé</option>
                                <option value="en_attente">En attente</option>
                            </select>
                        </div>

                        {{-- Observations --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-bold text-dark small">Observations / Référence bancaire</label>
                            <textarea name="description" id="description" class="form-control small" rows="2" placeholder="Ex: Solde d'hospitalisation, paiement par virement ou tiers...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                            <i data-lucide="check-circle" class="lucide-sm"></i>
                            <span>Valider l'Encaissement</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hiddenTicketId = document.getElementById('ticket_id');
    const cards = document.querySelectorAll('.ticket-card');
    const inputSearch = document.getElementById('ticket_search_input');
    const btnClearSearch = document.getElementById('btn_clear_ticket_search');

    const inputRecu = document.getElementById('montant_recu');
    const inputImpute = document.getElementById('montant_impute');
    const inputRendu = document.getElementById('montant_rendu');
    const btnToutSolder = document.getElementById('btn_tout_solder');

    const dispPatient = document.getElementById('disp_patient_name');
    const dispTotal = document.getElementById('disp_ticket_total');
    const dispReste = document.getElementById('disp_ticket_reste');
    const countLabel = document.getElementById('count_tickets_label');

    function formatFCFA(val) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(val)) + ' FCFA';
    }

    function selectCard(card) {
        if (!card) return;

        // Dé-sélectionner toutes les cartes
        cards.forEach(c => {
            c.classList.remove('selected');
            const check = c.querySelector('.ticket-check');
            if (check) {
                check.style.backgroundColor = '#f8fafc';
                check.style.borderColor = '#cbd5e1';
                const icon = check.querySelector('i, svg');
                if (icon) icon.classList.add('d-none');
            }
        });

        // Activer la carte choisie
        card.classList.add('selected');
        const check = card.querySelector('.ticket-check');
        if (check) {
            check.style.backgroundColor = '#0f766e';
            check.style.borderColor = '#0f766e';
            const icon = check.querySelector('i, svg');
            if (icon) icon.classList.remove('d-none');
        }

        // Mettre à jour l'ID caché
        hiddenTicketId.value = card.dataset.id;

        const reste = parseFloat(card.dataset.reste) || 0;
        const patient = card.dataset.patient || '--';
        const total = parseFloat(card.dataset.total) || 0;

        if (dispPatient) dispPatient.textContent = patient;
        if (dispTotal) dispTotal.textContent = formatFCFA(total);
        if (dispReste) dispReste.textContent = formatFCFA(reste);

        // Mettre à jour les montants du guichet
        inputImpute.value = reste;
        inputRecu.value = reste;
        calculerMonnaie();
    }

    // Événements de clic sur chaque carte
    cards.forEach(card => {
        card.addEventListener('click', function() {
            selectCard(this);
        });
    });

    function calculerMonnaie() {
        const recu = parseFloat(inputRecu.value) || 0;
        const impute = parseFloat(inputImpute.value) || 0;
        const rendu = Math.max(0, recu - impute);
        inputRendu.value = Math.round(rendu);
    }

    if (inputRecu) inputRecu.addEventListener('input', calculerMonnaie);
    if (inputImpute) inputImpute.addEventListener('input', calculerMonnaie);

    if (btnToutSolder) {
        btnToutSolder.addEventListener('click', function() {
            const selectedCard = document.querySelector('.ticket-card.selected');
            if (selectedCard) {
                const reste = parseFloat(selectedCard.dataset.reste) || 0;
                inputImpute.value = reste;
                inputRecu.value = reste;
                calculerMonnaie();
            }
        });
    }

    // Recherche / filtrage dynamique des cartes
    if (inputSearch) {
        inputSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const searchTxt = card.dataset.search || '';
                if (!q || searchTxt.includes(q)) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.classList.add('d-none');
                }
            });

            if (countLabel) {
                countLabel.textContent = visibleCount + ' ticket(s) trouvé(s)';
            }
        });

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', function() {
                inputSearch.value = '';
                cards.forEach(card => card.classList.remove('d-none'));
                if (countLabel) {
                    countLabel.textContent = cards.length + ' ticket(s) avec dette';
                }
            });
        }
    }

    // Initialisation : sélectionner la première carte ou la carte active
    const initiallySelected = document.querySelector('.ticket-card.selected') || cards[0];
    if (initiallySelected) {
        selectCard(initiallySelected);
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
<style>
.cursor-pointer { cursor: pointer; }
.extra-small { font-size: 0.78rem; }
.transition-all { transition: all 0.2s ease-in-out; }
.ticket-card {
    border: 1.5px solid #e2e8f0 !important;
    background-color: #ffffff;
}
.ticket-card:hover {
    border-color: #0f766e !important;
    background-color: #f0fdfa !important;
}
.ticket-card.selected {
    border-color: #0f766e !important;
    background-color: #f0fdfa !important;
    box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.12), 0 2px 4px -1px rgba(15, 118, 110, 0.06);
}
</style>
@endpush

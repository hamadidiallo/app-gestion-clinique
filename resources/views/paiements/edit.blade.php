@extends('layout')

@section('title', 'Modifier le Règlement - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-pencil-square me-2"></i>Modifier le Règlement : #{{ $paiement->reference }}
            </h1>
            <p class="text-muted mb-0">Mise à jour des informations de règlement et ajustement des montants encaissement.</p>
        </div>
        <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-gear-wide-connected text-primary me-2"></i>Édition de la Fiche de Règlement
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('paiements.update', $paiement) }}" method="POST" id="form_paiement_edit">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Référence --}}
                    <div class="col-md-6">
                        <label for="reference" class="form-label fw-bold">Référence du Reçu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                            <input type="text" name="reference" id="reference" class="form-control font-monospace border-start-0 @error('reference') is-invalid @enderror" value="{{ old('reference', $paiement->reference) }}" required>
                        </div>
                        @error('reference')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Mode de Paiement --}}
                    <div class="col-md-6">
                        <label for="mode_paiement_id" class="form-label fw-bold">Mode de Règlement <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-wallet2"></i></span>
                            <select name="mode_paiement_id" id="mode_paiement_id" class="form-select border-start-0 @error('mode_paiement_id') is-invalid @enderror" required>
                                @foreach($modePaiements as $mode)
                                    <option value="{{ $mode->id }}" {{ old('mode_paiement_id', $paiement->mode_paiement_id) == $mode->id ? 'selected' : '' }}>
                                        {{ $mode->nom }} ({{ $mode->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('mode_paiement_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- SECTEUR RECHERCHE ET SELECTION TICKET AVEC AUTOCOMPLETION --}}
                    <div class="col-md-12 bg-light p-3 rounded-3 border mb-2">
                        <label for="ticket_search_input" class="form-label fw-bold text-primary fs-6 mb-1">
                            <i class="bi bi-search me-1"></i>Ticket de Facturation & Recherche Rapide <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Recherchez instantanément par <strong>N° de Ticket, Nom, Prénom ou Téléphone du patient</strong>.</p>

                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white text-muted border-end-0">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                            <input type="text" 
                                   id="ticket_search_input" 
                                   class="form-control border-start-0 form-control-lg fs-6" 
                                   placeholder="Tapez ici le nom du patient, son n° de téléphone ou la référence ticket..."
                                   autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="btn_clear_ticket_search" title="Réinitialiser la recherche">
                                <i class="bi bi-x-circle-fill"></i> Effacer
                            </button>
                        </div>

                        <select name="ticket_id" id="ticket_id" class="form-select form-select-lg border @error('ticket_id') is-invalid @enderror" required size="5" style="max-height: 180px;">
                            @foreach($tickets as $ticket)
                                @php
                                    $patientNom = $ticket->patient->nom ?? 'Inconnu';
                                    $patientPrenom = $ticket->patient->prenom ?? '';
                                    $patientTel = $ticket->patient->telephone ?? '';
                                    $searchContent = strtolower("{$ticket->reference} {$patientNom} {$patientPrenom} {$patientTel}");
                                @endphp
                                <option value="{{ $ticket->id }}" 
                                        data-search="{{ $searchContent }}" 
                                        data-reste="{{ $ticket->reste_a_payer }}"
                                        {{ old('ticket_id', $paiement->ticket_id) == $ticket->id ? 'selected' : '' }}>
                                    Ticket #{{ $ticket->reference }} — {{ trim("{$patientNom} {$patientPrenom}") }} (Tél: {{ $patientTel ?: 'N/A' }}) | Reste: {{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FBU
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Type de payeur --}}
                    <div class="col-md-4">
                        <label for="type_payeur" class="form-label fw-bold">Type de Payeur <span class="text-danger">*</span></label>
                        <select name="type_payeur" id="type_payeur" class="form-select @error('type_payeur') is-invalid @enderror" required>
                            <option value="patient" {{ old('type_payeur', $paiement->type_payeur) == 'patient' ? 'selected' : '' }}>Patient (Part directe)</option>
                            <option value="assurance" {{ old('type_payeur', $paiement->type_payeur) == 'assurance' ? 'selected' : '' }}>Assurance (Prise en charge)</option>
                            <option value="tiers" {{ old('type_payeur', $paiement->type_payeur) == 'tiers' ? 'selected' : '' }}>Tiers Payeur</option>
                        </select>
                        @error('type_payeur')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Assurance --}}
                    <div class="col-md-4">
                        <label for="assurance_id" class="form-label fw-bold">Assurance (si applicable)</label>
                        <select name="assurance_id" id="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                            <option value="">-- Aucune assurance --</option>
                            @foreach($assurances as $assurance)
                                <option value="{{ $assurance->id }}" {{ old('assurance_id', $paiement->assurance_id) == $assurance->id ? 'selected' : '' }}>
                                    {{ $assurance->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('assurance_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Agent --}}
                    <div class="col-md-4">
                        <label for="user_id" class="form-label fw-bold">Agent Caissier <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $paiement->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- MONTANTS ET CALCUL EN TEMPS REEL --}}
                    <div class="col-md-4">
                        <label for="montant_recu" class="form-label fw-bold">1. Montant Donner / Reçu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" name="montant_recu" id="montant_recu" class="form-control form-control-lg fw-bold text-dark @error('montant_recu') is-invalid @enderror" value="{{ old('montant_recu', round($paiement->montant_recu)) }}" required>
                            <span class="input-group-text bg-light text-muted fw-bold">FBU</span>
                        </div>
                        @error('montant_recu')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="montant_impute" class="form-label fw-bold">2. Montant Imputé <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" name="montant_impute" id="montant_impute" class="form-control form-control-lg fw-bold text-success @error('montant_impute') is-invalid @enderror" value="{{ old('montant_impute', round($paiement->montant_impute)) }}" required>
                            <span class="input-group-text bg-light text-success fw-bold">FBU</span>
                        </div>
                        @error('montant_impute')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="montant_rendu" class="form-label fw-bold text-warning">3. Monnaie Rendue (Calcul Auto)</label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" name="montant_rendu" id="montant_rendu" class="form-control form-control-lg fw-bold text-warning bg-light" value="{{ old('montant_rendu', round($paiement->montant_rendu)) }}" readonly>
                            <span class="input-group-text bg-warning text-dark fw-bold">FBU</span>
                        </div>
                        @error('montant_rendu')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Date --}}
                    <div class="col-md-6">
                        <label for="date_paiement" class="form-label fw-bold">Date & Heure Règlement <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_paiement" id="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement', $paiement->date_paiement ? $paiement->date_paiement->format('Y-m-d\TH:i') : '') }}" required>
                        @error('date_paiement')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Statut --}}
                    <div class="col-md-6">
                        <label for="statut" class="form-label fw-bold">Statut du Règlement <span class="text-danger">*</span></label>
                        <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            @foreach($statuts as $key => $label)
                                <option value="{{ $key }}" {{ old('statut', $paiement->statut) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statut')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Observations --}}
                    <div class="col-md-12">
                        <label for="description" class="form-label fw-bold">Observations</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $paiement->description) }}</textarea>
                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ticket_search_input');
    const selectTickets = document.getElementById('ticket_id');
    const clearBtn = document.getElementById('btn_clear_ticket_search');
    const montantRecuInput = document.getElementById('montant_recu');
    const montantImputeInput = document.getElementById('montant_impute');
    const montantRenduInput = document.getElementById('montant_rendu');

    if (searchInput && selectTickets) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const options = selectTickets.options;

            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                if (!opt.value) continue;

                const searchData = opt.getAttribute('data-search') || opt.text.toLowerCase();
                if (searchData.includes(query)) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                const options = selectTickets.options;
                for (let i = 0; i < options.length; i++) {
                    options[i].style.display = '';
                }
                searchInput.focus();
            });
        }
    }

    function calculerMonnaieRendue() {
        const recu = parseFloat(montantRecuInput.value) || 0;
        const impute = parseFloat(montantImputeInput.value) || 0;
        const rendu = Math.max(0, recu - impute);
        montantRenduInput.value = Math.round(rendu);
    }

    if (montantRecuInput && montantImputeInput && montantRenduInput) {
        montantRecuInput.addEventListener('input', calculerMonnaieRendue);
        montantImputeInput.addEventListener('input', calculerMonnaieRendue);
        calculerMonnaieRendue();
    }
});
</script>
@endpush
@endsection

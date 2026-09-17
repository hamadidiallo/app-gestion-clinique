@extends('layout')

@section('title', 'Comptabiliser une Recette - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="graph-up-arrow" class="me-2"></i>Enregistrer une Recette Financière
            </h1>
            <p class="text-muted mb-0">Comptabilisation directe d'encaissements et revenus divers de la clinique.</p>
        </div>
        <a href="{{ route('recettes.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i data-lucide="arrow-left" class="me-1"></i> Retour au registre
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i data-lucide="banknote" class="text-primary me-2"></i>Informations sur la Recette
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('recettes.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form.input 
                            type="text" 
                            name="reference" 
                            label="Référence Recette" 
                            icon="bi-hash" 
                            :value="$defaultReference"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $ticketOptions = [];
                            foreach($tickets as $ticket) {
                                $ticketOptions[$ticket->id] = $ticket->reference . ' (Patient: ' . ($ticket->patient->nom ?? 'N/A') . ')';
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="ticket_id" 
                            label="Ticket Originel (Optionnel)" 
                            icon="bi-receipt" 
                            :options="$ticketOptions"
                            placeholder="-- Aucun ticket direct --"
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $paiementOptions = [];
                            foreach($paiements as $paiement) {
                                $paiementOptions[$paiement->id] = $paiement->reference . ' (' . number_format($paiement->montant_impute, 0, ',', ' ') . ' FBU)';
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="paiement_id" 
                            label="Règlement Associé (Optionnel)" 
                            icon="bi-credit-card-2-front" 
                            :options="$paiementOptions"
                            placeholder="-- Aucun paiement direct --"
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="number" 
                            name="montant" 
                            label="Montant Recette" 
                            icon="bi-currency-exchange" 
                            suffix="FBU"
                            step="1"
                            min="0"
                            placeholder="Ex: 100000" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $modeOptions = [];
                            foreach($modePaiements as $mode) {
                                $modeOptions[$mode->id] = $mode->nom;
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="mode_paiement_id" 
                            label="Mode d'Encaissement" 
                            icon="bi-wallet2" 
                            :options="$modeOptions"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $userOptions = [];
                            foreach($users as $user) {
                                $userOptions[$user->id] = $user->name ?? $user->nom;
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="user_id" 
                            label="Agent Enregistreur" 
                            icon="bi-person-badge" 
                            :options="$userOptions"
                            :value="auth()->id()"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="date" 
                            name="date_recette" 
                            label="Date de Recette" 
                            icon="bi-calendar-event" 
                            :value="date('Y-m-d')"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="select" 
                            name="statut" 
                            label="Statut" 
                            icon="bi-toggle-on" 
                            :options="$statuts" 
                            value="1"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-12">
                        <x-form.input 
                            type="textarea" 
                            name="description" 
                            label="Observations / Source de Recette" 
                            icon="bi-chat-left-text" 
                            placeholder="Saisir toute remarque ou provenance de cette recette..." 
                            rows="3"
                        />
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i data-lucide="check-circle" class="me-2"></i>Comptabiliser la Recette
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

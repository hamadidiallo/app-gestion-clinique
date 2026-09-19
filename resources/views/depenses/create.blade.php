@extends('layout')

@section('title', 'Saisir une Dépense - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="graph-down-arrow" class="me-2"></i>Enregistrer une Dépense / Charge
            </h1>
            <p class="text-muted mb-0">Comptabilisation des achats, factures d'eau/électricité et frais de fonctionnement.</p>
        </div>
        <a href="{{ route('depenses.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i data-lucide="arrow-left" class="me-1"></i> Retour au registre
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i data-lucide="receipt" class="text-primary me-2"></i>Détails de la Charge
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('depenses.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form.input 
                            type="text" 
                            name="reference" 
                            label="Référence Pièce / Facture" 
                            icon="bi-hash" 
                            :value="$defaultReference"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $catOptions = [];
                            foreach($categories as $cat) {
                                $catOptions[$cat->id] = $cat->nom . ' (' . $cat->code . ')';
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="categorie_depense_id" 
                            label="Catégorie de Dépense" 
                            icon="bi-tags" 
                            :options="$catOptions"
                            placeholder="-- Sélectionner la catégorie --"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="text" 
                            name="beneficiaire" 
                            label="Bénéficiaire / Fournisseur" 
                            icon="bi-building" 
                            placeholder="Ex: REGIDESO, Pharmamed..." 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="number" 
                            name="montant" 
                            label="Montant de la Dépense" 
                            icon="bi-currency-exchange" 
                            suffix="FCFA"
                            step="1"
                            min="0"
                            placeholder="Ex: 50000" 
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
                            label="Mode de Règlement" 
                            icon="bi-wallet2" 
                            :options="$modeOptions"
                            placeholder="-- Choisir un mode --"
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
                            name="date_depense" 
                            label="Date de la Dépense" 
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
                            label="Observations / Motif de la Dépense" 
                            icon="bi-chat-left-text" 
                            placeholder="Entrer les explications ou justifications..." 
                            rows="3"
                        />
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i data-lucide="check-circle" class="me-2"></i>Enregistrer la Dépense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

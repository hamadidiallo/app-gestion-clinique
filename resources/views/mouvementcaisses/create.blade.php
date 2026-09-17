@extends('layout')

@section('title', 'Nouveau Mouvement de Caisse - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="arrow-left-right" class="me-2"></i>Saisir un Mouvement de Caisse
            </h1>
            <p class="text-muted mb-0">Enregistrement des flux d'espèces (alimentations, retraits, transferts).</p>
        </div>
        <a href="{{ route('mouvementcaisses.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i data-lucide="arrow-left" class="me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i data-lucide="banknote" class="text-primary me-2"></i>Détails du Flux d'Espèces
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('mouvementcaisses.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form.input 
                            type="text" 
                            name="reference" 
                            label="Référence du Mouvement" 
                            icon="bi-hash" 
                            :value="$defaultReference"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        @php
                            $caisseOptions = [];
                            foreach($caisses as $caisse) {
                                $caisseOptions[$caisse->id] = 'Session #' . $caisse->id . ' (Responsable: ' . ($caisse->user->name ?? $caisse->user->nom ?? '') . ')';
                            }
                        @endphp
                        <x-form.input 
                            type="select" 
                            name="caisse_id" 
                            label="Caisse Cible" 
                            icon="bi-safe" 
                            :options="$caisseOptions"
                            placeholder="-- Choisir une caisse ouverte --"
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
                            label="Agent Opérateur" 
                            icon="bi-person-badge" 
                            :options="$userOptions"
                            :value="auth()->id()"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="select" 
                            name="type" 
                            label="Type de Mouvement" 
                            icon="bi-arrow-down-up" 
                            :options="$types"
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="text" 
                            name="origine" 
                            label="Origine / Motif" 
                            icon="bi-box-arrow-in-right" 
                            placeholder="Ex: Alimentation, Retrait de caisse, Avance..." 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-4">
                        <x-form.input 
                            type="number" 
                            name="montant" 
                            label="Montant d'Espèces" 
                            icon="bi-currency-exchange" 
                            suffix="FBU"
                            step="1"
                            min="0"
                            placeholder="Ex: 25000" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="datetime-local" 
                            name="date_mouvement" 
                            label="Date & Heure Mouvement" 
                            icon="bi-calendar-date" 
                            :value="date('Y-m-d\TH:i')"
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
                            label="Observations / Justifications" 
                            icon="bi-chat-left-text" 
                            placeholder="Fournir des précisions complémentaires sur ce mouvement d'espèces..." 
                            rows="3"
                        />
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i data-lucide="check-circle" class="me-2"></i>Valider le Mouvement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

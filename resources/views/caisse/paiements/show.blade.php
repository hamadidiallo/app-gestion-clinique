@extends('layout')

@section('title', 'Reçu de Paiement ' . $paiement->reference . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- Fil d'Ariane --}}
    <div class="small text-muted mb-2 d-flex align-items-center gap-1">
        <span>Caisse & Facturation</span>
        <span class="opacity-50">/</span>
        <a href="{{ route('paiements.index') }}" class="text-decoration-none text-muted">Journal des Paiements</a>
        <span class="opacity-50">/</span>
        <span class="fw-semibold text-dark">Quittance #{{ $paiement->reference }}</span>
    </div>

    {{-- Bannière Hero du Reçu --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
        <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 60px; height: 60px; background: linear-gradient(135deg, #0f766e, #14b8a6); flex-shrink: 0;">
                    <i data-lucide="badge-check" class="lucide-md"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h1 class="h4 text-dark fw-bold mb-0">Quittance de Caisse : {{ $paiement->reference }}</h1>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold rounded-pill">
                            🟢 Encaissé / Validé
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        Opération enregistrée le {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y à H:i') : $paiement->created_at->format('d/m/Y à H:i') }}
                        • Caissier : <strong class="text-dark">{{ $paiement->user->name ?? 'Caisse' }}</strong>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('paiements.print', $paiement) }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm fw-bold px-3" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                    <i data-lucide="printer" class="lucide-sm"></i>
                    <span>Imprimer Reçu (80mm)</span>
                </a>
                @if($paiement->ticket)
                    <a href="{{ route('tickets.show', $paiement->ticket) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                        <i data-lucide="external-link" class="lucide-sm"></i>
                        <span>Voir Ticket #{{ $paiement->ticket->reference }}</span>
                    </a>
                @endif
                <a href="{{ route('paiements.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" class="lucide-sm"></i>
                    <span>Journal</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Détails Financiers --}}
    <div class="row g-4">
        {{-- Colonne Gauche : Synthèse Financière --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="wallet" class="lucide-sm"></i>
                    </span>
                    Décompte de l'Encaissement
                </h5>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Somme Versée par le Débiteur :</span>
                        <strong class="font-mono text-dark fs-6">{{ number_format($paiement->montant_recu, 0, ',', ' ') }} FCFA</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Montant Imputé (Dette déduite) :</span>
                        <strong class="font-mono text-success fs-5">{{ number_format($paiement->montant_impute, 0, ',', ' ') }} FCFA</strong>
                    </li>
                    @if($paiement->montant_rendu > 0)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                            <span class="text-muted">Monnaie Rendue au Patient :</span>
                            <strong class="font-mono text-warning fs-6">{{ number_format($paiement->montant_rendu, 0, ',', ' ') }} FCFA</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Mode de Paiement :</span>
                        <span class="badge bg-light text-dark border fw-bold">{{ $paiement->modePaiement->nom ?? 'Espèces' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Type de Payeur :</span>
                        <span class="text-dark fw-semibold">{{ ucfirst($paiement->type_payeur) }} {{ $paiement->assurance ? '('.$paiement->assurance->nom.')' : '' }}</span>
                    </li>
                </ul>

                @if($paiement->ticket)
                    <div class="mt-4 p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Situation actuelle du ticket :</span>
                            @if($paiement->ticket->reste_a_payer <= 0)
                                <span class="badge bg-success fw-bold">Entièrement Soldé ✓</span>
                            @else
                                <span class="badge bg-danger fw-bold">Reste encore {{ number_format($paiement->ticket->reste_a_payer, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Colonne Droite : Patient & Traçabilité --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">
                        <i data-lucide="user" class="lucide-sm"></i>
                    </span>
                    Bénéficiaire & Ticket Associé
                </h5>

                @if($paiement->ticket && $paiement->ticket->patient)
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3 bg-light border">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #0f766e; flex-shrink: 0;">
                            {{ strtoupper(substr($paiement->ticket->patient->nom, 0, 1) . substr($paiement->ticket->patient->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('patients.show', $paiement->ticket->patient) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $paiement->ticket->patient->nom }} {{ $paiement->ticket->patient->prenom }}
                            </a>
                            <span class="text-muted extra-small font-mono">Tél : {{ $paiement->ticket->patient->telephone ?? 'N/A' }}</span>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <span class="text-muted small fw-bold d-block mb-1">Ticket Facturé :</span>
                    @if($paiement->ticket)
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border small">
                            <span class="font-mono fw-bold text-dark">#{{ $paiement->ticket->reference }}</span>
                            <span class="text-muted">Total Net : {{ number_format($paiement->ticket->montant_patient, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @else
                        <span class="text-muted small">Aucun ticket associé.</span>
                    @endif
                </div>

                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted extra-small">Imprimer un exemplaire client</span>
                    <a href="{{ route('paiements.print', $paiement) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i data-lucide="printer" class="lucide-xs"></i>
                        <span>Imprimer</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
<style>
.extra-small { font-size: 0.78rem; }
</style>
@endpush

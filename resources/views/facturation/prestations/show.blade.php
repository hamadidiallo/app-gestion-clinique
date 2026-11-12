@extends('layout')

@section('title', 'Prestation '.($prestation->reference ?? '#'.$prestation->id))

@section('content')
@php
    $fcfa = fn ($montant) => number_format((float) $montant, 0, ',', ' ');
    $montant = (float) $prestation->montant;
    $partAssurance = (float) $prestation->montant_assurance;
    $partPatient = $prestation->montant_patient !== null ? (float) $prestation->montant_patient : $montant - $partAssurance;
    $tauxCouverture = (float) $prestation->taux_couverture;
    $voitHonoraires = auth()->user()?->hasRole(['Administrateur', 'Comptable']);
    $voitTickets = auth()->user()?->hasRole(['Administrateur', 'Réceptionniste', 'Caissier', 'Comptable']);
    $tickets = $prestation->ticketDetails->pluck('ticket')->filter()->unique('id');
    $patient = $prestation->patient;
    $initiales = $patient ? mb_strtoupper(mb_substr($patient->prenom ?? '', 0, 1).mb_substr($patient->nom ?? '', 0, 1)) : '?';
@endphp

<div class="container-fluid p-0">
    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('prestations.index') }}" class="btn btn-light border bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour aux prestations" aria-label="Retour aux prestations">
                <i data-lucide="arrow-left" style="width: 1.15rem; height: 1.15rem;"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h3 text-dark fw-bold mb-0">Prestation <span class="font-mono text-teal">{{ $prestation->reference ?? '#'.$prestation->id }}</span></h1>
                    @if ($prestation->statut)
                        <span class="badge badge-success-pill px-2.5 py-1">Effectuée</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">Annulée</span>
                    @endif
                </div>
                <small class="text-muted">
                    {{ $prestation->type ?: 'Prestation médicale' }}
                    @if ($prestation->date_prestation)
                        &bull; Réalisée le <span class="font-mono">{{ $prestation->date_prestation->format('d/m/Y à H:i') }}</span>
                    @endif
                </small>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if ($voitTickets && $tickets->count() === 1)
                <a href="{{ route('tickets.show', $tickets->first()) }}" class="btn btn-outline-teal fw-semibold d-flex align-items-center gap-1">
                    <i data-lucide="receipt" style="width: 1rem; height: 1rem;"></i>
                    <span>Voir le ticket</span>
                </a>
            @endif
            <a href="{{ route('prestations.edit', $prestation) }}" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-1">
                <i data-lucide="edit-3" style="width: 1rem; height: 1rem;"></i>
                <span>Modifier</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Colonne principale --}}
        <div class="col-lg-8">
            {{-- Patient --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                <div class="card-body p-4">
                    <div class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: .06em; font-size: .72rem;">Patient</div>
                    @if ($patient)
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 52px; height: 52px; background: #e2f1ef; color: #0f766e; font-size: 1.05rem;">{{ $initiales }}</div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <a href="{{ route('patients.show', $patient) }}" class="h5 fw-bold text-dark text-decoration-none mb-1 d-inline-block">{{ $patient->prenom }} {{ $patient->nom }}</a>
                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                    @if ($patient->reference)<span class="font-mono">{{ $patient->reference }}</span>@endif
                                    @if ($patient->telephone)<span><i data-lucide="phone" style="width: .85rem; height: .85rem;"></i> {{ $patient->telephone }}</span>@endif
                                </div>
                            </div>
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-light border">Ouvrir le dossier</a>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun patient rattaché à cette prestation.</p>
                    @endif
                </div>
            </div>

            {{-- Prise en charge --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                <div class="card-body p-4">
                    <div class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: .06em; font-size: .72rem;">Prise en charge</div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 h-100" style="background: #f6f9f8;">
                                <div class="small text-muted mb-1">Service</div>
                                @if ($prestation->service)
                                    <a href="{{ route('services.show', $prestation->service) }}" class="fw-semibold text-dark text-decoration-none">{{ $prestation->service->nom }}</a>
                                    @if ($prestation->service->code)<div class="small font-mono text-muted">{{ $prestation->service->code }}</div>@endif
                                @else
                                    <span class="text-muted">Non attribué</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 h-100" style="background: #f6f9f8;">
                                <div class="small text-muted mb-1">Médecin</div>
                                @if ($prestation->medecin)
                                    <div class="fw-semibold">Dr {{ $prestation->medecin->prenom }} {{ mb_strtoupper($prestation->medecin->nom) }}</div>
                                    @if ($prestation->medecin->specialite)<div class="small text-muted">{{ $prestation->medecin->specialite }}</div>@endif
                                @else
                                    <span class="text-muted">Aucun médecin affecté</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: #f6f9f8;">
                                <div class="small text-muted mb-1">Acte de la nomenclature</div>
                                @if ($prestation->acte)
                                    <a href="{{ route('actes.show', $prestation->acte) }}" class="fw-semibold text-teal text-decoration-none">
                                        <span class="font-mono">{{ $prestation->acte->code }}</span> · {{ $prestation->acte->nom }}
                                    </a>
                                    <span class="small text-muted ms-1">tarif de base {{ $fcfa($prestation->acte->tarif_base) }} FCFA</span>
                                @else
                                    <span class="text-muted">Saisie libre, sans acte de la nomenclature.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Compte-rendu --}}
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-body p-4">
                    <div class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: .06em; font-size: .72rem;">Compte-rendu</div>
                    @if (filled($prestation->description))
                        <p class="mb-0" style="white-space: pre-line;">{{ $prestation->description }}</p>
                    @else
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 rounded-3" style="border: 1px dashed #cfdcd7;">
                            <span class="text-muted">Aucun compte-rendu n'a été saisi pour cette prestation.</span>
                            <a href="{{ route('prestations.edit', $prestation) }}" class="btn btn-sm btn-outline-teal">Ajouter un compte-rendu</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">
            {{-- Montant --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 14px;">
                <div class="p-4 text-white" style="background: #0f766e;">
                    <div class="small text-uppercase fw-bold mb-1" style="letter-spacing: .06em; font-size: .72rem; opacity: .85;">Montant facturé</div>
                    <div class="font-mono fw-bold" style="font-size: 2rem; line-height: 1.1;">{{ $fcfa($montant) }} <span style="font-size: 1rem;">FCFA</span></div>
                </div>
                <div class="card-body p-4">
                    @if ($partAssurance > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Part assurance{{ $tauxCouverture ? ' ('.rtrim(rtrim(number_format($tauxCouverture, 2, ',', ''), '0'), ',').' %)' : '' }}</span>
                            <span class="font-mono fw-semibold">{{ $fcfa($partAssurance) }}</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;" role="img" aria-label="Part assurance {{ $fcfa($partAssurance) }} FCFA sur {{ $fcfa($montant) }} FCFA">
                            <div class="progress-bar" style="width: {{ $montant > 0 ? round($partAssurance / $montant * 100) : 0 }}%; background: #0f766e;"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Part patient</span>
                            <span class="font-mono fw-bold">{{ $fcfa($partPatient) }} FCFA</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">À la charge du patient</span>
                            <span class="font-mono fw-bold">{{ $fcfa($montant) }} FCFA</span>
                        </div>
                        <div class="small text-muted mt-1">Aucune assurance ne couvre cette prestation.</div>
                    @endif
                </div>
            </div>

            {{-- Honoraires (administration et comptabilité uniquement) --}}
            @if ($voitHonoraires && ((float) $prestation->part_medecin > 0 || (float) $prestation->part_clinique > 0))
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4">
                        <div class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: .06em; font-size: .72rem;">Répartition des honoraires</div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Médecin{{ $prestation->pourcentage_medecin ? ' ('.(float) $prestation->pourcentage_medecin.' %)' : '' }}</span>
                            <span class="font-mono fw-semibold">{{ $fcfa($prestation->part_medecin) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Clinique{{ $prestation->pourcentage_clinique ? ' ('.(float) $prestation->pourcentage_clinique.' %)' : '' }}</span>
                            <span class="font-mono fw-semibold">{{ $fcfa($prestation->part_clinique) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ticket et historique --}}
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-body p-4">
                    <div class="small text-uppercase fw-bold text-muted mb-3" style="letter-spacing: .06em; font-size: .72rem;">Facturation</div>
                    @forelse ($tickets as $ticket)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Ticket</span>
                            @if ($voitTickets)
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-mono fw-semibold text-teal text-decoration-none">{{ $ticket->reference }}</a>
                            @else
                                <span class="font-mono fw-semibold">{{ $ticket->reference }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="small text-muted mb-2">Pas encore rattachée à un ticket.</p>
                    @endforelse
                    <hr class="my-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Réalisée le</span>
                        <span class="font-mono">{{ $prestation->date_prestation?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Enregistrée le</span>
                        <span class="font-mono">{{ $prestation->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

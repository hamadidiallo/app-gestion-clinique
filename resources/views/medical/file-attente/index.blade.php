@extends('layout')

@section('title', "Ma file d'attente")

@section('content')
<div class="container-fluid p-0">
    @if (session('alert'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2" role="status">
            <i data-lucide="check-circle" class="lucide-sm"></i> {{ session('alert') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-triangle" class="lucide-sm"></i> {{ session('error') }}
        </div>
    @endif

    @if (! $medecin)
        {{-- Compte non relié à une fiche médecin --}}
        <div class="card border-0 shadow-sm mx-auto mt-4" style="max-width: 640px; border-radius: 16px;">
            <div class="card-body p-5 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #fff4e5; color: #b54708;">
                    <i data-lucide="link-2-off" style="width: 28px; height: 28px;"></i>
                </div>
                <h1 class="h4 fw-bold mb-2">Votre compte n'est relié à aucune fiche médecin</h1>
                <p class="text-muted mb-0">Demandez à l'administrateur de la clinique d'ouvrir votre fiche dans <strong>Médecins Praticiens</strong> et de choisir votre compte dans le champ « Compte de connexion ». Votre file d'attente apparaîtra ensuite ici.</p>
            </div>
        </div>
    @else
        @php
            $patientNom = fn ($patient) => $patient ? mb_strtoupper($patient->nom).' '.$patient->prenom : 'Patient anonyme';
        @endphp

        {{-- En-tête --}}
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <div class="text-muted small mb-1">{{ ucfirst(now()->translatedFormat('l j F Y')) }}</div>
                <h1 class="h3 fw-bold mb-0">Bonjour Dr {{ $medecin->nom }}</h1>
                <div class="text-muted">{{ $medecin->service?->nom ?? $medecin->specialite }}</div>
            </div>
            <div class="d-flex gap-2">
                <div class="px-3 py-2 rounded-3 bg-white shadow-sm text-center" style="min-width: 110px;">
                    <div class="font-mono fs-4 fw-bold text-teal">{{ $enAttente->count() }}</div>
                    <div class="small text-muted">en attente</div>
                </div>
                <div class="px-3 py-2 rounded-3 bg-white shadow-sm text-center" style="min-width: 110px;">
                    <div class="font-mono fs-4 fw-bold" style="color: #b54708;">{{ $enConsultation->count() }}</div>
                    <div class="small text-muted">en consultation</div>
                </div>
                <div class="px-3 py-2 rounded-3 bg-white shadow-sm text-center" style="min-width: 110px;">
                    <div class="font-mono fs-4 fw-bold text-dark">{{ $vusAujourdhui->count() }}</div>
                    <div class="small text-muted">vus aujourd'hui</div>
                </div>
            </div>
        </div>

        {{-- Consultations ouvertes --}}
        @if ($enConsultation->isNotEmpty())
            <div class="mb-4">
                @foreach ($enConsultation as $consultation)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-2 rounded-3 shadow-sm" style="background: #fffaeb; border: 1px solid #fedf89;">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge rounded-pill" style="background: #b54708;">En consultation</span>
                            <div>
                                <div class="fw-bold">{{ $patientNom($consultation->patient) }}</div>
                                <div class="small text-muted">{{ $consultation->motif_consultation }} · ouverte à {{ $consultation->date_consultation?->format('H:i') }}</div>
                            </div>
                        </div>
                        <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-warning fw-semibold">Reprendre la consultation</a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-8">
                {{-- Mes patients --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                        <h2 class="h5 fw-bold mb-0">Mes patients en attente</h2>
                        <span class="small text-muted">par ordre d'arrivée</span>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        @forelse ($enAttente as $ticket)
                            @include('medical.file-attente._patient', ['ticket' => $ticket, 'position' => $loop->iteration])
                        @empty
                            <div class="text-center text-muted py-5">
                                <i data-lucide="coffee" class="d-block mx-auto mb-2 opacity-50" style="width: 32px; height: 32px;"></i>
                                Aucun patient en attente pour vous. Les patients apparaissent ici dès qu'ils ont payé leur ticket à la caisse.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Patients du service sans médecin --}}
                <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h2 class="h5 fw-bold mb-1">À prendre en charge</h2>
                        <div class="small text-muted">
                            @if ($medecin->service)
                                Patients de {{ $medecin->service->nom }} sans médecin attribué. Le premier médecin qui les prend en charge les reçoit.
                            @else
                                Aucun service principal n'est renseigné sur votre fiche : demandez à l'administrateur de l'ajouter pour voir ces patients.
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        @forelse ($aPrendre as $ticket)
                            @include('medical.file-attente._patient', ['ticket' => $ticket, 'position' => null])
                        @empty
                            <div class="text-muted small py-3">Aucun patient à prendre en charge dans votre service pour le moment.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Vus aujourd'hui --}}
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                        <h2 class="h5 fw-bold mb-0">Vus aujourd'hui</h2>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        @forelse ($vusAujourdhui as $consultation)
                            <a href="{{ route('consultations.show', $consultation) }}" class="d-flex justify-content-between align-items-center py-2 text-decoration-none text-dark border-bottom">
                                <span>
                                    <span class="fw-semibold d-block">{{ $patientNom($consultation->patient) }}</span>
                                    <span class="small text-muted">{{ \Illuminate\Support\Str::limit($consultation->diagnostic ?: $consultation->motif_consultation, 40) }}</span>
                                </span>
                                <span class="small font-mono text-muted">{{ $consultation->date_consultation?->format('H:i') }}</span>
                            </a>
                        @empty
                            <div class="text-muted small py-3">Aucune consultation terminée aujourd'hui.</div>
                        @endforelse
                    </div>
                </div>
                <p class="small text-muted mt-3 mb-0">
                    <i data-lucide="refresh-cw" class="lucide-sm"></i> La file se met à jour toute seule chaque minute.
                </p>
            </div>
        </div>
    @endif
</div>

@if ($medecin)
    @push('scripts')
        <script>
            // Actualisation automatique : les nouveaux patients payés apparaissent sans recharger à la main
            setTimeout(function () {
                if (document.visibilityState === 'visible') {
                    window.location.reload();
                }
            }, 60000);
        </script>
    @endpush
@endif
@endsection
